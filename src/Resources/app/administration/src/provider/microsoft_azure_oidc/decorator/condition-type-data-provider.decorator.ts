import {HeptacomOauthRuleDataProviderService} from '../../../service/heptacom-oauth-rule-data-provider.service';

export default (ruleConditionService: HeptacomOauthRuleDataProviderService) => {
    ruleConditionService.addCondition('heptacomAdminOpenAuthMicrosoftAzureOidcGroups', {
        component: 'sw-condition-generic',
        label: 'heptacomAdminOpenAuthClient.providerFields.microsoft_azure_oidc.condition.rule.groupIds',
        scopes: ['microsoft_azure_oidc'],
        group: 'user',
    });

    return ruleConditionService;
};
