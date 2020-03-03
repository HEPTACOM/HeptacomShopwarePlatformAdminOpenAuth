import template from './heptacom-admin-open-auth-client-edit-page.html.twig';

const { Component, Context, Mixin } = Shopware;

Component.register('heptacom-admin-open-auth-client-edit-page', {
    template,

    inject: [
        'repositoryFactory',
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
            item: null
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
        }
    },

    methods: {
        loadData() {
            this.isLoading = true;

            this.loadClient().then(() => {
                this.isLoading = false;
            });
        },

        loadClient() {
            this.item = null;

            return this.clientRepository
                .get(this.clientId, Context.api)
                .then(item => {
                    this.item = item;
                });
        },

        cancelEdit() {
            this.$router.push({ name: this.$route.meta.parentPath });
        },

        saveItem() {
            this.isLoading = true;

            this.clientRepository.save(this.item, Context.api).then(() => {
                this.isLoading = false;
                this.isSaveSuccessful = true;

                return this.loadData();
            }).catch(exception => {
                this.isLoading = false;
                const clientName = this.client.name;
                this.createNotificationError({
                    title: this.$tc('global.notification.notificationSaveErrorTitle'),
                    message: this.$tc(
                        'global.notification.notificationSaveErrorMessage', 0, { entityName: clientName }
                    )
                });

                throw exception;
            });
        }
    }
});
