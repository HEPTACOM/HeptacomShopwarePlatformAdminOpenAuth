import './sw-verify-user-modal.scss';
import template from './sw-verify-user-modal.html.twig';

const { Component, Context } = Shopware;
const heptacomAdminOpenAuthConfirmState = 'HeptacomAdminOpenAuthConfirmState';

Component.override('sw-verify-user-modal', {
    template,

    inject: [
        'loginService',
        'repositoryFactory',
    ],

    data() {
        return {
            heptacomAdminOpenAuthLoading: true,
            heptacomAdminOpenAuthClients: [],
            heptacomAdminOpenAuthWaitingForConfirmation: false,
            heptacomAdminOpenAuthConfirmationClient: null,
        }
    },

    computed: {
        heptacomAdminOpenAuthClientsRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_client')
        },

        heptacomAdminOpenAuthHttpClient() {
            return this.heptacomAdminOpenAuthClientsRepository.httpClient;
        }
    },

    methods: {
        async createdComponent() {
            this.loadHeptacomAdminOpenAuth();

            return this.$super('createdComponent');
        },

        loadHeptacomAdminOpenAuth() {
            this.heptacomAdminOpenAuthLoading = true;
            this.heptacomAdminOpenAuthClients = [];

            this.getHeptacomAdminOpenAuthConnectedClients().then(result => {
                this.heptacomAdminOpenAuthClients = result;
            }).finally(() => {
                this.heptacomAdminOpenAuthLoading = false;
            });
        },

        startHeptacomAdminOpenAuthFlow(client) {
            this.heptacomAdminOpenAuthWaitingForConfirmation = true;
            this.heptacomAdminOpenAuthConfirmationClient = client;
            localStorage.removeItem(heptacomAdminOpenAuthConfirmState);

            this.getHeptacomAdminOpenAuthConfirmRedirectUrl(client.id)
                .then((url) => {
                    const left = Math.floor((screen.width - 600 ) / 2);
                    const top = Math.floor((screen.height - 600 ) / 2);
                    const oauthWindow = window.open(
                        url,
                        'Confirm with ' + client.name,
                        `location=0,status=0,width=600,height=600, top=${top}, left=${left}`
                    );
                    const windowLoop = window.setInterval(() => {
                        if (!this.heptacomAdminOpenAuthWaitingForConfirmation) {
                            window.clearInterval(windowLoop);
                            oauthWindow.close();

                            return;
                        }

                        if (oauthWindow.closed) {
                            window.clearInterval(windowLoop);

                            this.heptacomAdminOpenAuthWaitingForConfirmation = false;
                            const statePayload = localStorage.getItem(heptacomAdminOpenAuthConfirmState);

                            if (statePayload) {
                                this.verifyHeptacomAdminOpenAuthByState(JSON.parse(statePayload).state)
                            }
                        }
                    }, 1000);
                });
        },

        onCloseConfirmPasswordModal() {
            this.heptacomAdminOpenAuthConfirmationClient = null;
            this.heptacomAdminOpenAuthWaitingForConfirmation = false;

            return this.$super('onCloseConfirmPasswordModal');
        },

        getHeptacomAdminOpenAuthConnectedClients() {
            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);

            return this.heptacomAdminOpenAuthHttpClient
                .get(`/_admin/open-auth/client/list`, { headers })
                .then(response => {
                    return response.data.filter(client => client.connected);
                });
        },

        getHeptacomAdminOpenAuthConfirmRedirectUrl(clientId) {
            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);

            return this.heptacomAdminOpenAuthHttpClient
                .get(`/_admin/open-auth/${clientId}/confirm`, { headers })
                .then(response => {
                    return response.data.target;
                });
        },

        verifyHeptacomAdminOpenAuthByState(state) {
            this.heptacomAdminOpenAuthHttpClient.post('/oauth/token', {
                grant_type: 'heptacom_admin_open_auth_one_time_token',
                client_id: 'administration',
                scope: 'user-verified',
                one_time_token: state
            }, {
                baseURL: Context.api.apiPath
            }).then((response) => {
                const context = { ...Context.api };
                context.authToken.access = response.data.access_token;

                const authObject = {
                    ...this.loginService.getBearerAuthentication(),
                    ...{
                        access: context.authToken.access,
                    },
                };

                this.loginService.setBearerAuthentication(authObject);

                this.$emit('verified', context);
            });
        }
    }
});
