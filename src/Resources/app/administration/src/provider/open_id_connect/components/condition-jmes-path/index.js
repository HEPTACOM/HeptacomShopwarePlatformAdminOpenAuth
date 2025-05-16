import template from './condition-jmes-path.html.twig';
import './condition-jmes-path.scss';

const { Component } = Shopware;
const { mapPropertyErrors } = Component.getComponentHelper();

export default {
    template,

    computed: {
        jmesPathExpression: {
            get() {
                this.ensureValueExist();
                return this.condition.value.jmesPathExpression || null;
            },
            set(jmesPathExpression) {
                this.ensureValueExist();
                this.condition.value = { ...this.condition.value, jmesPathExpression: jmesPathExpression };
            },
        },

        ...mapPropertyErrors('condition', ['value.jmesPathExpression']),

        currentError() {
            return this.conditionValueJmesPathExpressionError;
        },
    },
};
