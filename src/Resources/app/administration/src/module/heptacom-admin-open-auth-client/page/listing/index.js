import template from './heptacom-admin-open-auth-client-listing-page.html.twig';
import './heptacom-admin-open-auth-client-listing-page.scss';

const { Context, Data, Mixin } = Shopware;
const { Criteria } = Data;

export default {
    template,

    inject: [
        'acl',
        'repositoryFactory',
    ],

    mixins: [
        Mixin.getByName('listing')
    ],

    data() {
        return {
            isLoading: true,
            items: null,
        }
    },

    created() {
        this.getList();
    },

    computed: {
        clientRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_client');
        },

        clientCriteria() {
            const result = new Criteria();
            const params = this.getMainListingParams();

            result.addAssociation('userKeys');
            result.setLimit(params.limit);
            result.setPage(params.page);
            result.addSorting(Criteria.sort(params.sortBy || 'name', params.sortDirection || 'ASC'));

            if (params.term && params.term.length) {
                result.addFilter(Criteria.contains('name', params.term));
            }

            return result;
        },

        columns() {
            return [
                {
                    property: 'active',
                    label: this.$t('heptacom-admin-open-auth-client.pages.listing.columns.active'),
                    allowResize: false,
                    width: '50px'
                }, {
                    property: 'name',
                    label: this.$t('heptacom-admin-open-auth-client.pages.listing.columns.name'),
                    routerLink: 'heptacom.admin.open.auth.client.edit'
                }, {
                    property: 'provider',
                    label: this.$t('heptacom-admin-open-auth-client.pages.listing.columns.provider')
                }, {
                    property: 'userKeys.length',
                    label: this.$t('heptacom-admin-open-auth-client.pages.listing.columns.users'),
                    width: '100px'
                }, {
                    property: 'createdAt',
                    label: this.$t('heptacom-admin-open-auth-client.pages.listing.columns.createdAt'),
                    width: '200px'
                }
            ];
        },

        dateFilter() {
            return Shopware.Filter.getByName('date');
        }
    },

    methods: {
        getList() {
            return this.loadData();
        },

        loadData() {
            this.isLoading = true;

            this.loadClients().then(() => {
                this.isLoading = false;
            });
        },

        loadClients() {
            this.items = null;

            return this.clientRepository
                .search(this.clientCriteria, Context.api)
                .then(items => {
                    this.items = items;
                });
        },

        getLoginColor(client) {
            if (!client.active) {
                return '#333333';
            }

            if (client.login) {
                return '#00cc00'
            }

            return '#cc0000';
        },

        getConnectColor(client) {
            if (!client.active) {
                return '#333333';
            }

            if (client.connect) {
                return '#00cc00'
            }

            return '#cc0000';
        }
    }
};
