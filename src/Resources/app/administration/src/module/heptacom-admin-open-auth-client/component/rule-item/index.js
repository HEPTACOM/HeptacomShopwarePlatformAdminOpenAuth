import './rule-item.scss';
import template from './rule-item.html.twig';

export default {
    template,

    inject: [
        'repositoryFactory',
        'heptacomOauthRuleDataProvider'
    ],

    props: {
        isLoading: {
            required: false,
            type: Boolean,
            default: false,
        },
        position: {
            required: true,
            type: Number,
        },
        client: {
            required: true,
            type: Object,
        },
        rule: {
            required: true,
            type: Object,
        },
    },

    computed: {
        conditionRepository () {
            return this.repositoryFactory.create('heptacom_admin_open_auth_client_rule_condition');
        },

        scopes() {
            return [
                'global',
                this.client.provider
            ];
        }
    }
}