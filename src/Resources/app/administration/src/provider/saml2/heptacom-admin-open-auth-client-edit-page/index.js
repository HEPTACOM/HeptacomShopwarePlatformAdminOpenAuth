import template from './heptacom-admin-open-auth-client-edit-page.html.twig';

const { Component } = Shopware;

Component.override('heptacom-admin-open-auth-client-edit-page', {
    template,

    data() {
        return {
            availableProperties: [
                'objectIdentifier',
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
            if (newValue && newValue.provider === 'saml2') {
                if (!newValue.config.attributeMapping) {
                    newValue.config.attributeMapping = [
                        {
                            shopwareAttribute: 'firstName',
                            samlAttribute: 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
                        },
                        {
                            shopwareAttribute: 'lastName',
                            samlAttribute: 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname',
                        },
                        {
                            shopwareAttribute: 'email',
                            samlAttribute: 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
                        },
                    ];
                }
            }
        }
    }
});
