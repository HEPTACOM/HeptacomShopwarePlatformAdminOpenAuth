import template from './heptacom-admin-open-auth-url-clients.html.twig';

const { Context } = Shopware;

export default {
    template,

    inject: [
        'repositoryFactory',
    ],

    props: {
        userId: {
            type: String,
            required: false,
            default: null,
        },
        isUserLoading: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    data() {
        return {
            heptacomAdminOpenAuthLoading: true,
            heptacomAdminOpenAuthClients: [],
        }
    },

    computed: {
        heptacomAdminOpenAuthClientsRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_client')
        },

        heptacomAdminOpenAuthHttpClient() {
            return this.heptacomAdminOpenAuthClientsRepository.httpClient;
        },

        isMe() {
            return Shopware.State.get('session').currentUser.id === this.userId;
        }
    },

    watch: {
        isUserLoading: {
            handler() {
                this.loadHeptacomAdminOpenAuth().then()
            },
        },
    },

    created() {
        this.loadHeptacomAdminOpenAuth().then();
    },

    methods: {
        async loadHeptacomAdminOpenAuth() {
            if (this.isUserLoading || !this.userId) {
                return;
            }

            console.log(this.userId);

            this.heptacomAdminOpenAuthLoading = true;
            this.heptacomAdminOpenAuthClients = [];

            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);
            const response = await this.heptacomAdminOpenAuthHttpClient
                .get(`/_admin/open-auth/client/list`, {
                    headers,
                    params: {
                        userId: this.userId,
                    },
                });

            this.heptacomAdminOpenAuthClients = response.data.data;
            this.heptacomAdminOpenAuthLoading = false;
        },

        async redirectToLoginMask(clientId) {
            const currentPath = window.location.pathname + window.location.search + window.location.hash;

            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);
            const response = await this.heptacomAdminOpenAuthHttpClient
                .post(`/_action/open-auth/${clientId}/connect?redirectTo=${encodeURIComponent(currentPath)}`, {}, { headers });

            window.location.href = response.data.target;
        },

        async revokeHeptacomAdminOpenAuthUserKey(clientId) {
            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);
            await this.heptacomAdminOpenAuthHttpClient
                .post(`/_action/open-auth/${clientId}/disconnect`, {}, { headers });

            await this.loadHeptacomAdminOpenAuth();
        },
    },
};
