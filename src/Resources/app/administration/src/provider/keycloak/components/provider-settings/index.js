import template from './provider-settings.html.twig';

const { Component } = Shopware;

Component.register('heptacom-admin-open-auth-provider-keycloak-settings', {
    template,

    props: {
        item: {
            required: true,
        },
    },

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
            if (!newValue.config.scopes) {
                newValue.config.scopes = [];
            }
        }
    }
});
