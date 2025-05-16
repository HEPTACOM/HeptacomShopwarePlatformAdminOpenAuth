import microsoftEntraIdOidcDataProvider from './microsoft_entra_id_oidc/decorator/condition-type-data-provider.decorator';
import oidcDataProvider from './open_id_connect/decorator/condition-type-data-provider.decorator';
import saml2DataProvider from './saml2/decorator/condition-type-data-provider.decorator';

const { Application, Component } = Shopware;

// Cidaas
Component.register('heptacom-admin-open-auth-provider-cidaas-settings', () => import ('./cidaas/components/provider-settings'));

// Google Cloud
Component.register('heptacom-admin-open-auth-provider-google-cloud-settings', () => import ('./google_cloud/components/provider-settings'));

// Jira
Component.register('heptacom-admin-open-auth-provider-jira-settings', () => import ('./jira/components/provider-settings'));

// Jumpcloud
Component.register('heptacom-admin-open-auth-provider-jumpcloud-settings', () => import ('./jumpcloud/components/provider-settings'));

// Keycloak
Component.register('heptacom-admin-open-auth-provider-keycloak-settings', () => import ('./keycloak/components/provider-settings'));

// Microsoft Entra ID
Component.register('heptacom-admin-open-auth-provider-microsoft-entra-id-oidc-settings', () => import ('./microsoft_entra_id_oidc/components/provider-settings'));
Application.addServiceProviderDecorator('heptacomOauthRuleDataProvider', microsoftEntraIdOidcDataProvider);

// Okta
Component.register('heptacom-admin-open-auth-provider-okta-settings', () => import ('./okta/components/provider-settings'));

// One Login
Component.register('heptacom-admin-open-auth-provider-onelogin-settings', () => import ('./onelogin/components/provider-settings'));

// OpenID Connect
Component.extend('heptacom-admin-open-auth-condition-authenticated-request', 'sw-condition-base', () => import ('./open_id_connect/components/condition-authenticated-request'));
Component.register('heptacom-admin-open-auth-provider-open-id-connect-settings', () => import ('./open_id_connect/components/provider-settings'));
Application.addServiceProviderDecorator('heptacomOauthRuleDataProvider', oidcDataProvider);

// SAML2
Component.register('heptacom-admin-open-auth-provider-saml2-settings', () => import ('./saml2/components/provider-settings'));
Application.addServiceProviderDecorator('heptacomOauthRuleDataProvider', saml2DataProvider);

import './jumpcloud/overrides/heptacom-admin-open-auth-client-edit-page';
import './saml2/overrides/heptacom-admin-open-auth-client-edit-page';
