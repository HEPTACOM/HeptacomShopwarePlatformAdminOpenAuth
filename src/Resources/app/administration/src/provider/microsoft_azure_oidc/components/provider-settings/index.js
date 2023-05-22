import template from './provider-settings.html.twig';

const { Component } = Shopware;

Component.register('heptacom-admin-open-auth-provider-microsoft-azure-oidc-settings', {
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