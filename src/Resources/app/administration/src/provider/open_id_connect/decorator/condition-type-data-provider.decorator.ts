import {HeptacomOauthRuleDataProviderService} from '../../../service/heptacom-oauth-rule-data-provider.service';

export default (ruleConditionService: HeptacomOauthRuleDataProviderService) => {
    ruleConditionService.addCondition('heptacomAdminOpenAuthAuthenticatedRequest', {
        component: 'heptacom-admin-open-auth-condition-authenticated-request',
        label: 'heptacomAdminOpenAuthClient.providerFields.open_id_connect.condition.rule.authenticatedRequest',
        scopes: ['open_id_connect', 'cidaas', 'google_cloud', 'keycloak', 'microsoft_entra_id_oidc', 'okta', 'onelogin'],
        group: 'user',
    });

    ruleConditionService.addCondition('heptacomAdminOpenAuthAuthenticatedODataRequest', {
        component: 'heptacom-admin-open-auth-condition-authenticated-request',
        label: 'heptacomAdminOpenAuthClient.providerFields.open_id_connect.condition.rule.authenticatedODataRequest',
        scopes: ['open_id_connect', 'cidaas', 'google_cloud', 'keycloak', 'microsoft_entra_id_oidc', 'okta', 'onelogin'],
        group: 'user',
    });

    ruleConditionService.addCondition('heptacomAdminOpenAuthIdToken', {
        component: 'heptacom-admin-open-auth-condition-jmes-path',
        label: 'heptacomAdminOpenAuthClient.providerFields.open_id_connect.condition.rule.idToken',
        scopes: ['open_id_connect', 'cidaas', 'google_cloud', 'keycloak', 'microsoft_entra_id_oidc', 'okta', 'onelogin'],
        group: 'user',
    });

    return ruleConditionService;
};
