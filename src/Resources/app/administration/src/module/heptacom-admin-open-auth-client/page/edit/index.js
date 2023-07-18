import './heptacom-admin-open-auth-client-edit-page.scss';
import template from './heptacom-admin-open-auth-client-edit-page.html.twig';

const { Component, Context, Mixin, Data } = Shopware;
const { Criteria } = Data;

Component.register('heptacom-admin-open-auth-client-edit-page', {
    template,

    inject: [
        'acl',
        'repositoryFactory',
        'HeptacomAdminOpenAuthProviderApiService',
    ],

    mixins: [
        Mixin.getByName('placeholder'),
        Mixin.getByName('notification'),
    ],

    props: {
        clientId: {
            required: true,
            type: String
        }
    },

    data() {
        return {
            isLoading: true,
            isSaveSuccessful: false,
            item: null,
            showDeleteModal: false,
            redirectUri: null,
            metadataUri: null
        }
    },

    created() {
        this.loadData();
    },

    computed: {
        dynamicName() {
            return this.placeholder(this.item, 'name', this.$t('heptacom-admin-open-auth-client.pages.edit.title'));
        },

        clientRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_client');
        },

        clientCriteria() {
            const criteria = new Criteria();
            criteria.addAssociation('defaultAclRoles');

            return criteria;
        },

        providerSettingsComponent() {
            let provider = (this.item && this.item.provider ? this.item.provider : '')
                .replace(/_/g, '-');

            return `heptacom-admin-open-auth-provider-${provider}-settings`;
        },

        providerSettingsProps() {
            return {
                item: this.item,
            };
        },
    },

    methods: {
        loadData() {
            this.isLoading = true;

            this.loadClient().finally(() => {
                this.isLoading = false;
            });
        },

        async loadClient() {
            this.item = null;

            this.item = await this.clientRepository.get(this.clientId, Context.api, this.clientCriteria);
            this.redirectUri = (await this.HeptacomAdminOpenAuthProviderApiService.getRedirectUri(this.item.id)).target;
            this.metadataUri = (await this.HeptacomAdminOpenAuthProviderApiService.getMetadataUri(this.item.id)).target;
        },

        cancelEdit() {
            this.$router.push({ name: this.$route.meta.parentPath });
        },

        saveItem() {
            this.isLoading = true;

            this.clientRepository
                .save(this.item, Context.api)
                .then(() => {
                    this.isSaveSuccessful = true;

                    return this.loadData();
                })
                .catch(exception => {
                    const clientName = this.client.name;
                    this.createNotificationError({
                        title: this.$tc('global.notification.notificationSaveErrorTitle'),
                        message: this.$tc(
                            'global.notification.notificationSaveErrorMessage', 0, { entityName: clientName }
                        )
                    });

                    throw exception;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        onConfirmDelete() {
            this.showDeleteModal = false;
            this.isLoading = true;

            return this.clientRepository
                .delete(this.item.id, Context.api)
                .then(() => {
                    this.$router.push({ name: 'heptacom.admin.open.auth.client.settings' });
                })
                .catch(exception => {
                    const clientName = this.client.name;
                    this.createNotificationError({
                        title: this.$tc('global.notification.notificationSaveErrorTitle'),
                        message: this.$tc(
                            'global.notification.notificationSaveErrorMessage', 0, { entityName: clientName }
                        )
                    });

                    throw exception;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        }
    }
});
