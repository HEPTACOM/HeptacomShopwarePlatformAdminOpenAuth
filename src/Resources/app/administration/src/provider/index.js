const { Component } = Shopware;

Component.register('heptacom-admin-open-auth-provider-cidaas-settings', () => import ('./cidaas/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-google-cloud-settings', () => import ('./google_cloud/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-jira-settings', () => import ('./jira/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-jumpcloud-settings', () => import ('./jumpcloud/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-keycloak-settings', () => import ('./keycloak/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-microsoft-azure-oidc-settings', () => import ('./microsoft_azure_oidc/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-okta-settings', () => import ('./okta/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-onelogin-settings', () => import ('./onelogin/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-open-id-connect-settings', () => import ('./open_id_connect/components/provider-settings'));
Component.register('heptacom-admin-open-auth-provider-saml2-settings', () => import ('./saml2/components/provider-settings'));

import './jumpcloud/overrides/heptacom-admin-open-auth-client-edit-page';
import './saml2/overrides/heptacom-admin-open-auth-client-edit-page';
