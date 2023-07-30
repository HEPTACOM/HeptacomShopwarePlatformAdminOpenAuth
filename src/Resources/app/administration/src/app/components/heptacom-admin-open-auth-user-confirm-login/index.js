import './heptacom-admin-open-auth-user-confirm-login.scss';
import template from './heptacom-admin-open-auth-user-confirm-login.html.twig';

const { Component, Context } = Shopware;
const confirmStateKey = 'HeptacomAdminOpenAuthConfirmState';

export default {
    template,

    inject: [
        'loginService',
        'repositoryFactory',
    ],

    props: {
        divider: {
            type: Boolean,
            default: true,
        },
    },

    data() {
        return {
            loading: true,
            clients: [],
            waitingForConfirmation: false,
            confirmationClient: null,
        }
    },

    computed: {
        heptacomAdminOpenAuthClientsRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_client')
        },

        httpClient() {
            return this.heptacomAdminOpenAuthClientsRepository.httpClient;
        },

        sectionDivider() {
            return this.divider ? 'bottom' : '';
        }
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            this.loadClients();
        },

        loadClients() {
            this.loading = true;
            this.clients = [];

            this.getConnectedClients().then(result => {
                this.clients = result;
            }).finally(() => {
                this.loading = false;
            });


        },

        startAuthFlow(client) {
            this.waitingForConfirmation = true;
            this.confirmationClient = client;
            localStorage.removeItem(confirmStateKey);

            this.getConfirmRedirectUrl(client.id)
                .then((url) => {
                    const left = Math.floor((screen.width - 600 ) / 2);
                    const top = Math.floor((screen.height - 600 ) / 2);
                    const oauthWindow = window.open(
                        url,
                        this.$t('heptacom-admin-open-auth-user-confirm-login.confirmWith', { 'clientName': client.name }),
                        `location=0,status=0,width=600,height=600, top=${top}, left=${left}`
                    );
                    const windowLoop = window.setInterval(() => {
                        if (!this.waitingForConfirmation) {
                            window.clearInterval(windowLoop);
                            oauthWindow.close();

                            return;
                        }

                        if (oauthWindow.closed) {
                            window.clearInterval(windowLoop);

                            this.waitingForConfirmation = false;
                            const statePayload = localStorage.getItem(confirmStateKey);

                            if (statePayload) {
                                this.verifyByState(JSON.parse(statePayload).state)
                            }
                        }
                    }, 1000);
                });
        },

        abortAuthFlow() {
            this.confirmationClient = null;
            this.waitingForConfirmation = false;
        },

        getConnectedClients() {
            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);

            return this.httpClient
                .get(`/_admin/open-auth/client/list`, { headers })
                .then(response => {
                    return response.data.data.filter(client => client.connected);
                });
        },

        getConfirmRedirectUrl(clientId) {
            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);

            return this.httpClient
                .get(`/_admin/open-auth/${clientId}/confirm`, { headers })
                .then(response => {
                    return response.data.target;
                });
        },

        verifyByState(state) {
            this.httpClient.post('/oauth/token', {
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

                this.$emit('confirm', context);
            });
        }
    }
};
