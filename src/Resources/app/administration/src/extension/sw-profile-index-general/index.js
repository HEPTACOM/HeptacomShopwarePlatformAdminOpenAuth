import template from './sw-profile-index-general.html.twig';

const { Component, Context } = Shopware;

Component.override('sw-profile-index-general', {
    template,

    inject: [
        'repositoryFactory',
        'systemConfigApiService',
    ],

    data() {
        return {
            denyPasswordLogin: false,
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
    },

    watch: {
        isUserLoading: {
            handler() {
                this.loadHeptacomAdminOpenAuth().then()
            },
        },
        languages: {
            handler() {
                this.loadHeptacomAdminOpenAuth().then()
            },
        },
    },

    created() {
        this.systemConfigApiService.getValues('KskHeptacomAdminOpenAuth.config').then((response) => {
            this.denyPasswordLogin = response['KskHeptacomAdminOpenAuth.config.denyPasswordLogin'];
        });
    },

    methods: {
        async loadHeptacomAdminOpenAuth() {
            if (this.isUserLoading || !this.languages) {
                return;
            }

            this.heptacomAdminOpenAuthLoading = true;
            this.heptacomAdminOpenAuthClients = [];

            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);
            const response = await this.heptacomAdminOpenAuthHttpClient
                .get(`/_admin/open-auth/client/list`, { headers });

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
});
