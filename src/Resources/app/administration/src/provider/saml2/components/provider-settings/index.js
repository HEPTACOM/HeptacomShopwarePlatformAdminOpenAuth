import template from './provider-settings.html.twig';

export default {
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
                'locale',
                'roles',
            ],
            availableAuthnContexts: [
                'urn:oasis:names:tc:SAML:2.0:ac:classes:unspecified',
                'urn:oasis:names:tc:SAML:2.0:ac:classes:Kerberos',
                'urn:oasis:names:tc:SAML:2.0:ac:classes:Password',
                'urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport',
                'urn:oasis:names:tc:SAML:2.0:ac:classes:Smartcard',
                'urn:oasis:names:tc:SAML:2.0:ac:classes:SmartcardPKI',
                'urn:oasis:names:tc:SAML:2.0:ac:classes:TLSClient',
                'urn:oasis:names:tc:SAML:2.0:ac:classes:TimeSyncToken',
                'urn:oasis:names:tc:SAML:2.0:ac:classes:X509',
                'urn:federation:authentication:windows',
            ].map(context => ({
                value: context,
                label: context.startsWith('urn:oasis:names:tc:SAML:2.0:ac:classes:') ? context.substring('urn:oasis:names:tc:SAML:2.0:ac:classes:'.length) : context
            })),
            attributeMappingTemplates: {
                friendlyNames: {
                    firstName: 'givenName',
                    lastName: 'surName',
                    email: 'emailAddress',
                    roles: 'memberOf'
                },
                x500: {
                    firstName: 'urn:oid:2.5.4.42',
                    lastName: 'urn:oid:2.5.4.4',
                    email: 'urn:oid:1.2.840.113549.1.9.1',
                    roles: 'urn:oid:1.3.6.1.4.1.5923.1.5.1.1'
                },
                entraId: {
                    firstName: 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
                    lastName: 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname',
                    email: 'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
                    roles: 'http://schemas.microsoft.com/ws/2008/06/identity/claims/role',
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
};
