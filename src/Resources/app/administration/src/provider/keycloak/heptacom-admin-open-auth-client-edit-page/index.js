import template from './heptacom-admin-open-auth-client-edit-page.html.twig';

const { Component } = Shopware;

Component.override('heptacom-admin-open-auth-client-edit-page', {
    template,

    data() {
        return {
            jsonPlaceholder: JSON.stringify({
                realm: "master",
                'auth-server-url': "https://keycloak.my-company.com/auth/",
                'ssl-required': "external",
                resource: "my-client",
                credentials: {
                    secret: "abcdefghijgklmnopqrstuvwxyz"
                },
                'confidential-port': 0
            }, null, '\t')
        }
    },

    watch: {
        item(newValue) {
            if (newValue && newValue.provider === 'keycloak') {
                if (!newValue.config.scopes) {
                    newValue.config.scopes = [];
                }
            }
        }
    }
});
