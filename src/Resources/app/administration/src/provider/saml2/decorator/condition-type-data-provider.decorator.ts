import {HeptacomOauthRuleDataProviderService} from '../../../service/heptacom-oauth-rule-data-provider.service';

export default (ruleConditionService: HeptacomOauthRuleDataProviderService) => {
    ruleConditionService.addCondition('heptacomAdminOpenAuthSaml2Role', {
        component: 'sw-condition-generic',
        label: 'heptacomAdminOpenAuthClient.providerFields.saml2.condition.rule.roles',
        scopes: ['saml2'],
        group: 'user',
    });

    return ruleConditionService;
};
