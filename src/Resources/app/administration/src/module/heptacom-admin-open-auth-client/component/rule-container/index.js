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
        action: {
            required: true,
            type: Array,
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

        sortedRules() {
            return this.client.rules
                .filter(rule => rule.actionName === this.action.name)
                .sort((a, b) => a.position - b.position);
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
            rule.actionName = this.action.name;
            rule.actionConfig = {};
            rule.position = this.client.rules.length;
            this.client.rules.add(rule);
        },

        conditionsDeleted(deletedIds) {
            this.$emit('conditions-deleted', deletedIds);
        },

        moveRuleUp(rule) {
            this.swapRules(rule.position, rule.position - 1);
        },

        moveRuleDown(rule) {
            this.swapRules(rule.position, rule.position + 1);
        },

        deleteRule(rule) {
            this.$emit('rule-deleted', rule.id);
            this.client.rules.remove(rule.id);

            let position = 0;
            for (const rule of this.sortedRules) {
                rule.position = position;
                position++;
            }
        },

        swapRules(positionA, positionB) {
            let ruleA = null;
            let ruleB = null;

            for (const rule of this.client.rules) {
                if (ruleA === null && rule.position === positionA) {
                    ruleA = rule;
                    continue;
                }

                if (ruleB === null && rule.position === positionB) {
                    ruleB = rule;
                    continue;
                }
            }

            if (ruleA === null || ruleB === null) {
                return;
            }

            ruleA.position = positionB;
            ruleB.position = positionA;
        }
    }
}
