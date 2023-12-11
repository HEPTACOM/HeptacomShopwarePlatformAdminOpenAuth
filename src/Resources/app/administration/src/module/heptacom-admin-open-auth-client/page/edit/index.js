import './heptacom-admin-open-auth-client-edit-page.scss';
import template from './heptacom-admin-open-auth-client-edit-page.html.twig';

const { Context, Mixin, Data } = Shopware;
const { Criteria } = Data;

export default {
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
            metadataUri: null,
            deletedRuleIds: [],
            deletedConditionIds: [],
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

        ruleRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_client_rule');
        },

        ruleConditionRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_client_rule_condition');
        },

        clientCriteria() {
            const criteria = new Criteria();
            criteria.getAssociation('rules').addSorting(Criteria.sort('position', 'ASC'));
            criteria.addAssociation('rules.aclRoles');
            criteria.addAssociation('rules.conditions');
            criteria.addAssociation('defaultAclRoles');

            return criteria;
        },

        providerSlug() {
            return (this.item && this.item.provider ? this.item.provider : '')
                .replace(/_/g, '-');
        },

        roleAssignmentTypes() {
            return [
                {
                    label: this.$tc('heptacom-admin-open-auth-client.pages.edit.roleAssignmentType.static'),
                    value: 'static',
                },
                {
                    label: this.$tc('heptacom-admin-open-auth-client.pages.edit.roleAssignmentType.dynamic'),
                    value: 'dynamic',
                },
            ];
        },

        providerRoleAssignmentComponent() {
            return `heptacom-admin-open-auth-provider-${this.providerSlug}-role-assignment`;
        },

        providerRoleAssignmentProps() {
            return {
                isLoading: this.isLoading,
                client: this.item,
            };
        },

        providerSettingsComponent() {
            return `heptacom-admin-open-auth-provider-${this.providerSlug}-settings`;
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

            this.saveClient()
                .then(this.syncDeletedConditions)
                .then(this.syncDeletedRules)
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

        saveClient() {
            return this.clientRepository.save(this.item, Context.api);
        },

        syncDeletedRules() {
            if (this.deletedRuleIds.length > 0) {
                return this.ruleRepository.syncDeleted(this.deletedRuleIds, Context.api).then(() => {
                    this.deletedRuleIds = [];
                });
            }
        },

        syncDeletedConditions() {
            if (this.deletedConditionIds.length > 0) {
                return this.ruleConditionRepository.syncDeleted(this.deletedConditionIds, Context.api).then(() => {
                    this.deletedConditionIds = [];
                });
            }
        },

        onRuleDeleted(deletedId) {
            this.deletedRuleIds.push(deletedId);
        },

        onConditionsDeleted(deletedIds) {
            this.deletedConditionIds = [...this.deletedConditionIds, ...deletedIds];
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
};
