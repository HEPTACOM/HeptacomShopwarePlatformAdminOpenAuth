import template from './provider-settings.html.twig';

const { Component } = Shopware;

Component.register('heptacom-admin-open-auth-provider-jumpcloud-settings', {
    template,

    props: {
        item: {
            required: true,
        },
    },

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
            if (!newValue.config.attributeMapping) {
                newValue.config.attributeMapping = {};
            }
        }
    },
});
