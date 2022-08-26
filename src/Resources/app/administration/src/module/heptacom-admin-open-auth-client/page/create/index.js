import './heptacom-admin-open-auth-client-create-page.scss';
import template from './heptacom-admin-open-auth-client-create-page.html.twig';

const { Component } = Shopware;

Component.register('heptacom-admin-open-auth-client-create-page', {
    template,

    inject: [
        'HeptacomAdminOpenAuthProviderApiService',
    ],

    data() {
        return {
            isLoading: true,
            items: null
        }
    },

    created() {
        this.loadData();
    },

    methods: {
        loadData() {
            this.isLoading = true;

            this.loadProviders().then(() => {
                this.isLoading = false;
            });
        },

        loadProviders() {
            this.items = [];

            return this.HeptacomAdminOpenAuthProviderApiService
                .list()
                .then(items => {
                    this.items = items.data.map(key => ({
                        key,
                        label: this.$t(`heptacomAdminOpenAuthClient.providers.${key}.label`),
                        logoFile: this.$t(`heptacomAdminOpenAuthClient.providers.${key}.logoFile`),
                        actionLabel: this.$te(`.heptacomAdminOpenAuthClient.providersCreate.${key}`) ?
                            this.$t(`heptacomAdminOpenAuthClient.providersCreate.${key}`) :
                            this.$t('heptacom-admin-open-auth-client.pages.create.actions.create'),
                        classes: [
                            'heptacom-admin-open-auth-client-create-page-providers-provider',
                            `heptacom-admin-open-auth-client-create-page-providers--provider-${key}`,
                        ],
                    }))
                    .sort((a, b) =>
                        a.label.localeCompare(b.label)
                    )
                    .filter((provider) =>
                        provider.key !== 'microsoft_azure'
                    );
                    this.isLoading = false;
                });
        },

        createClient(provider) {
            return this.HeptacomAdminOpenAuthProviderApiService
                .factorize(provider.key)
                .then(response => {
                    this.$router.push({ name: 'heptacom.admin.open.auth.client.edit', params: { id: response.data.id } });
                });
        }
    }
});
