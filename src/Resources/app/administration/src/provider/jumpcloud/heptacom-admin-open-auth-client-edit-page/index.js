import template from './heptacom-admin-open-auth-client-edit-page.html.twig';

const { Component } = Shopware;

Component.override('heptacom-admin-open-auth-client-edit-page', {
    template,

    data() {
        return {
            availableProperties: [
                'firstName',
                'lastName',
                'email',
                'timezone',
                'locale'
            ],
        };
    },

    watch: {
        item(newValue) {
            if (newValue && newValue.provider === 'jumpcloud') {
                if (!newValue.config.attributeMapping) {
                    newValue.config.attributeMapping = {};
                }
            }
        }
    },
});
