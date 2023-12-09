const { Component } = Shopware;

// Cidaas
Component.register('heptacom-admin-open-auth-provider-cidaas-settings', () => import ('./cidaas/components/provider-settings'));
Component.extend('heptacom-admin-open-auth-provider-cidaas-role-assignment', 'heptacom-admin-open-auth-provider-open-id-connect-role-assignment', {});

// Google Cloud
Component.register('heptacom-admin-open-auth-provider-google-cloud-settings', () => import ('./google_cloud/components/provider-settings'));
Component.extend('heptacom-admin-open-auth-provider-google-role-assignment', 'heptacom-admin-open-auth-provider-open-id-connect-role-assignment', {});

// Jira
Component.register('heptacom-admin-open-auth-provider-jira-settings', () => import ('./jira/components/provider-settings'));

// Jumpcloud
Component.register('heptacom-admin-open-auth-provider-jumpcloud-settings', () => import ('./jumpcloud/components/provider-settings'));
Component.extend('heptacom-admin-open-auth-provider-jumpcloud-role-assignment', 'heptacom-admin-open-auth-provider-saml2-role-assignment', {});

// Keycloak
Component.register('heptacom-admin-open-auth-provider-keycloak-settings', () => import ('./keycloak/components/provider-settings'));
Component.extend('heptacom-admin-open-auth-provider-keycloak-role-assignment', 'heptacom-admin-open-auth-provider-open-id-connect-role-assignment', {});

// Microsoft Azure
Component.register('heptacom-admin-open-auth-provider-microsoft-azure-oidc-settings', () => import ('./microsoft_azure_oidc/components/provider-settings'));
Component.extend('heptacom-admin-open-auth-provider-okta-role-assignment', 'heptacom-admin-open-auth-provider-open-id-connect-role-assignment', {});

// Okta
Component.register('heptacom-admin-open-auth-provider-okta-settings', () => import ('./okta/components/provider-settings'));
Component.extend('heptacom-admin-open-auth-provider-okta-role-assignment', 'heptacom-admin-open-auth-provider-open-id-connect-role-assignment', {});

// One Login
Component.register('heptacom-admin-open-auth-provider-onelogin-settings', () => import ('./onelogin/components/provider-settings'));
Component.extend('heptacom-admin-open-auth-provider-onelogin-role-assignment', 'heptacom-admin-open-auth-provider-open-id-connect-role-assignment', {});

// OpenID Connect
Component.register('heptacom-admin-open-auth-provider-open-id-connect-settings', () => import ('./open_id_connect/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-open-id-connect-role-assignment', () => import ('./open_id_connect/components/provider-settings'));

// SAML2
Component.register('heptacom-admin-open-auth-provider-saml2-settings', () => import ('./saml2/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-saml2-role-assignment', () => import ('./saml2/components/role-assignment'));

import './jumpcloud/overrides/heptacom-admin-open-auth-client-edit-page';
import './saml2/overrides/heptacom-admin-open-auth-client-edit-page';
