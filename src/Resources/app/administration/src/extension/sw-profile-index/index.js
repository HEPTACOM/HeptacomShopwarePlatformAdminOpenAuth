import template from './sw-profile-index.html.twig';

const { Component, Context } = Shopware;

Component.override('sw-profile-index', {
    template,

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
    },

    methods: {
        loadHeptacomAdminOpenAuth() {
            this.heptacomAdminOpenAuthLoading = true;

            this.heptacomAdminOpenAuthClients = [];

            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);

            return this.heptacomAdminOpenAuthHttpClient
                .get(`/_admin/open-auth/client/list`, { headers })
                .then(response => {
                    this.heptacomAdminOpenAuthClients = response.data.data;
                    this.heptacomAdminOpenAuthLoading = false;
                });
        },

        redirectToLoginMask(clientId) {
            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);

            this.heptacomAdminOpenAuthHttpClient
                .post(`/_action/open-auth/${clientId}/connect`, {}, { headers })
                .then(response => {
                    window.location.href = response.data.target;
                });
        },

        revokeHeptacomAdminOpenAuthUserKey(clientId) {
            const headers = this.heptacomAdminOpenAuthClientsRepository.buildHeaders(Context.api);

            this.heptacomAdminOpenAuthHttpClient
                .post(`/_action/open-auth/${clientId}/disconnect`, {}, { headers })
                .then(response => {
                    this.loadHeptacomAdminOpenAuth()
                });
        },

        getUserData() {
            return this.$super('getUserData').then(user => {
                return this.loadHeptacomAdminOpenAuth().then(() => user);
            })
        }
    }
});
