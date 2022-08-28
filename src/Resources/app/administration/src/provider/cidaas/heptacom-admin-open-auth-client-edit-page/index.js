import template from './heptacom-admin-open-auth-client-edit-page.html.twig';

const { Component } = Shopware;

Component.override('heptacom-admin-open-auth-client-edit-page', {
    template,

    watch: {
        item(newValue) {
            if (newValue && newValue.provider === 'cidaas') {
                if (!newValue.config.scopes) {
                    newValue.config.scopes = [];
                }
            }
        }
    }
});
