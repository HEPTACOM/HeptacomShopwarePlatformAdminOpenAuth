import template from './provider-settings.html.twig';

const { Component } = Shopware;

Component.register('heptacom-admin-open-auth-provider-saml2-settings', {
    template,

    props: {
        item: {
            required: true,
        },
    },

    data() {
        return {
            selectedMappingTemplate: null,
            availableProperties: [
                'firstName',
                'lastName',
                'email',
                'timezone',
                'locale'
            ],
            attributeMappingTemplates: {
                friendlyNames: {
                    firstName: 'givenName',
                    lastName: 'surName',
                    email: 'emailAddress',
                },
                x500: {
                    firstName: 'urn:oid:2.5.4.42',
                    lastName: 'urn:oid:2.5.4.4',
                    email: 'urn:oid:1.2.840.113549.1.9.1',
                },
                azure: {
                    firstName: 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
                    lastName: 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname',
                    email: 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
                },
            }
        };
    },

    watch: {
        item(newValue) {
            if (!newValue.config.attributeMapping) {
                newValue.config.attributeMapping = {};
            }
        }
    },

    methods: {
        onApplyMappingTemplate(templateKey) {
            const mappingTemplate = this.attributeMappingTemplates[templateKey];

            this.item.config.attributeMapping = Object.assign(
                this.item.config.attributeMapping,
                mappingTemplate
            );
        },
    },
});
