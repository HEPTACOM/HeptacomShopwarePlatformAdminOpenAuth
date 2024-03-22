"use strict";(window["webpackJsonpPluginksk-heptacom-admin-open-auth"]=window["webpackJsonpPluginksk-heptacom-admin-open-auth"]||[]).push([[314],{314:function(e,t,i){i.r(t),i.d(t,{default:function(){return a}});var a={template:'{% block heptacom_admin_open_auth_provider_saml2_settings %}\n    <div>\n        {% block heptacom_admin_open_auth_provider_saml2_settings_idp %}\n            <sw-card\n                :title="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderCardTitle\')"\n                position-identifier="heptacom-admin-open-auth-provider-saml2-settings-idp"\n            >\n                \n                <sw-url-field\n                    :omitUrlHash="true"\n                    :omitUrlSearch="false"\n                    :disabled="!!item.config.identityProviderMetadataXml"\n                    :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderMetadataUrl\')"\n                    :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderMetadataUrl-help\')"\n                    :placeholder="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderMetadataUrl-placeholder\')"\n                    v-model:value="item.config.identityProviderMetadataUrl"\n                ></sw-url-field>\n                <sw-textarea-field\n                    :disabled="!!item.config.identityProviderMetadataUrl"\n                    :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderMetadataXml\')"\n                    :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderMetadataXml-help\')"\n                    v-model:value="item.config.identityProviderMetadataXml"\n                ></sw-textarea-field>\n\n                \n                <sw-text-field\n                    :disabled="!!item.config.identityProviderMetadataUrl || !!item.config.identityProviderMetadataXml"\n                    :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderEntityId\')"\n                    v-model:value="item.config.identityProviderEntityId"\n                ></sw-text-field>\n                <sw-url-field\n                    :omitUrlHash="true"\n                    :omitUrlSearch="true"\n                    :disabled="!!item.config.identityProviderMetadataUrl || !!item.config.identityProviderMetadataXml"\n                    :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderSsoUrl\')"\n                    v-model:value="item.config.identityProviderSsoUrl"\n                ></sw-url-field>\n                <sw-textarea-field\n                    :disabled="!!item.config.identityProviderMetadataUrl || !!item.config.identityProviderMetadataXml"\n                    :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderCertificate\')"\n                    v-model:value="item.config.identityProviderCertificate"\n                ></sw-textarea-field>\n            </sw-card>\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_provider_saml2_settings_security %}\n            <sw-card\n                :title="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.security.cardTitle\')"\n                position-identifier="heptacom-admin-open-auth-provider-saml2-settings-security"\n            >\n                <sw-multi-select :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.security.requestedAuthnContexts\')"\n                                 :options="availableAuthnContexts"\n                                 :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.security.requestedAuthnContextsHelp\')"\n                                 :placeholder="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.security.requestedAuthnContextsPlaceholder\')"\n                                 v-model:value="item.config.requestedAuthnContext"\n                >\n                </sw-multi-select>\n            </sw-card>\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_provider_saml2_settings_attribute_mapping %}\n            <sw-card\n                :title="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.cardTitle\')"\n                position-identifier="heptacom-admin-open-auth-provider-saml2-settings-attribute-mapping"\n            >\n                {% block heptacom_admin_open_auth_provider_saml2_settings_attribute_mapping_inner %}\n                    <sw-select-field :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.templateSelect\')"\n                                     :placeholder="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.templateSelect-placeholder\')"\n                                     v-model:value="selectedMappingTemplate"\n                    >\n                        <template v-for="(mappedAttributes, templateKey) of attributeMappingTemplates">\n                            <option :value="templateKey">\n                                {{ $t(\'heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.template.\' + templateKey) }}\n                            </option>\n                        </template>\n                    </sw-select-field>\n\n                    <div class="sw-field">\n                        <sw-button\n                            :block="true"\n                            variant="ghost"\n                            size="small"\n                            :disabled="!selectedMappingTemplate"\n                            @click="onApplyMappingTemplate(selectedMappingTemplate)"\n                        >\n                            {{ $t(\'heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.templateApply\') }}\n                        </sw-button>\n                    </div>\n\n                    <template v-for="mappedProperty of availableProperties">\n                        <sw-text-field :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.field.\' + mappedProperty)"\n                                       v-model:value="item.config.attributeMapping[mappedProperty]"\n                        ></sw-text-field>\n                    </template>\n                {% endblock %}\n            </sw-card>\n        {% endblock %}\n    </div>\n{% endblock %}\n',props:{item:{required:!0}},data(){return{selectedMappingTemplate:null,availableProperties:["firstName","lastName","email","timezone","locale","roles"],availableAuthnContexts:["urn:oasis:names:tc:SAML:2.0:ac:classes:unspecified","urn:oasis:names:tc:SAML:2.0:ac:classes:Kerberos","urn:oasis:names:tc:SAML:2.0:ac:classes:Password","urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport","urn:oasis:names:tc:SAML:2.0:ac:classes:Smartcard","urn:oasis:names:tc:SAML:2.0:ac:classes:SmartcardPKI","urn:oasis:names:tc:SAML:2.0:ac:classes:TLSClient","urn:oasis:names:tc:SAML:2.0:ac:classes:TimeSyncToken","urn:oasis:names:tc:SAML:2.0:ac:classes:X509","urn:federation:authentication:windows"].map(e=>({value:e,label:e.startsWith("urn:oasis:names:tc:SAML:2.0:ac:classes:")?e.substring(39):e})),attributeMappingTemplates:{friendlyNames:{firstName:"givenName",lastName:"surName",email:"emailAddress",roles:"memberOf"},x500:{firstName:"urn:oid:2.5.4.42",lastName:"urn:oid:2.5.4.4",email:"urn:oid:1.2.840.113549.1.9.1",roles:"urn:oid:1.3.6.1.4.1.5923.1.5.1.1"},azure:{firstName:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname",lastName:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname",email:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress",roles:"http://schemas.microsoft.com/ws/2008/06/identity/claims/role"}}}},watch:{item(e){e.config.attributeMapping||(e.config.attributeMapping={})}},methods:{onApplyMappingTemplate(e){let t=this.attributeMappingTemplates[e];this.item.config.attributeMapping=Object.assign(this.item.config.attributeMapping,t)}}}}}]);
//# sourceMappingURL=cbc2f1d9bca2eaf82fe7.js.map