import template from './sw-profile-index.html.twig';

const { Component, Context, Data } = Shopware;
const { Criteria } = Data;

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

        heptacomAdminOpenAuthUserEmailsRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_user_email')
        },

        heptacomAdminOpenAuthUserKeysRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_user_key')
        },

        heptacomAdminOpenAuthUserTokensRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_user_token')
        }
    },

    methods: {
        loadHeptacomAdminOpenAuth(userId) {
            this.heptacomAdminOpenAuthLoading = true;

            this.heptacomAdminOpenAuthClients = [];
            const criteria = new Criteria();
            criteria.getAssociation('userKeys').addFilter(Criteria.equals('userId', userId));
            criteria.getAssociation('userEmails').addFilter(Criteria.equals('userId', userId));
            criteria.getAssociation('userTokens').addFilter(Criteria.equals('userId', userId));

            return this.heptacomAdminOpenAuthClientsRepository
                .search(criteria, Context.api)
                .then(result => {
                    this.heptacomAdminOpenAuthClients = result;
                    this.heptacomAdminOpenAuthLoading = false;
                });
        },

        revokeHeptacomAdminOpenAuthUserKey(item) {
            return Promise.all([
                    ...item.userKeys.map(key =>
                        this.heptacomAdminOpenAuthUserKeysRepository.delete(key.id, Context.api),
                    ),
                    ...item.userEmails.map(email =>
                        this.heptacomAdminOpenAuthUserEmailsRepository.delete(email.id, Context.api)
                    ),
                    ...item.userTokens.map(token =>
                        this.heptacomAdminOpenAuthUserTokensRepository.delete(token.id, Context.api)
                    )
                ])
                .then(() => this.loadHeptacomAdminOpenAuth(this.user.id));
        },

        redirectToLoginMask(clientId) {
            this.heptacomAdminOpenAuthClientsRepository
                .httpClient
                .get(`/_admin/open-auth/${clientId}/connect`)
                .then(response => {
                    window.location.href = response.data.target;
                });
        },

        getUserData() {
            return this.$super('getUserData').then(user => {
                return this.loadHeptacomAdminOpenAuth(user.id).then(() => user);
            })
        }
    }
});
