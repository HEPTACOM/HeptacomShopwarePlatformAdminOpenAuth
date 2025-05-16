import {HeptacomOauthRuleDataProviderService} from '../../../service/heptacom-oauth-rule-data-provider.service';

export default (ruleConditionService: HeptacomOauthRuleDataProviderService) => {
    ruleConditionService.addCondition('heptacomAdminOpenAuthMicrosoftEntraIdOidcGroups', {
        component: 'sw-condition-generic',
        label: 'heptacomAdminOpenAuthClient.providerFields.microsoft_entra_id_oidc.condition.rule.groupIds',
        scopes: ['microsoft_entra_id_oidc'],
        group: 'user',
    });

    return ruleConditionService;
};
