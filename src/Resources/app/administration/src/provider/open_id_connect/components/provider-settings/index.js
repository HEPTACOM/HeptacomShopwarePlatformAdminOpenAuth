import template from './provider-settings.html.twig';

const { Component } = Shopware;

Component.register('heptacom-admin-open-auth-provider-open-id-connect-settings', {
    template,

    props: {
        item: {
            required: true,
        },
    },

    watch: {
        item(newValue) {
            if (!newValue.config.scopes) {
                newValue.config.scopes = [];
            }
        }
    }
});
