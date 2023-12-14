import template from './condition-authenticated-request.html.twig';
import './condition-authenticated-request.scss';

const { Component } = Shopware;
const { mapPropertyErrors } = Component.getComponentHelper();

export default {
    template,

    computed: {
        requestUrl: {
            get() {
                this.ensureValueExist();
                return this.condition.value.requestUrl || null;
            },
            set(requestUrl) {
                this.ensureValueExist();
                this.condition.value = { ...this.condition.value, requestUrl: requestUrl };
            },
        },

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

        ...mapPropertyErrors('condition', ['value.requestUrl', 'value.jmesPathExpression']),

        currentError() {
            return this.conditionValueRequestUrlError || this.conditionValueJmesPathExpressionError;
        },
    },
};
