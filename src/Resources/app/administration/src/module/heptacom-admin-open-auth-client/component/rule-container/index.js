import template from './rule-container.html.twig';

export default {
    template,

    inject: [
        'repositoryFactory',
        'ruleConditionsConfigApiService'
    ],

    props: {
        client: {
            required: true,
            type: Object,
        },
    },

    data() {
        return {
            isLoading: false,
        };
    },

    computed: {
        ruleRepository() {
            return this.repositoryFactory.create('heptacom_admin_open_auth_client_rule');
        },
    },


    created() {
        this.createdComponent().then();
    },

    methods: {
        async createdComponent() {
            this.isLoading = true;

            await this.loadConditionData();

            this.isLoading = false;
        },

        async loadConditionData() {
            await this.ruleConditionsConfigApiService.load();
        },

        addRule() {
            const rule = this.ruleRepository.create();
            rule.clientId = this.client.id;
            this.client.rules.add(rule);
        },
    }
}
