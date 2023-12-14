import {HeptacomOauthRuleDataProviderService} from './heptacom-oauth-rule-data-provider.service';

export default (ruleConditionService: HeptacomOauthRuleDataProviderService) => {
    ruleConditionService.addCondition('alwaysValid', {
        component: 'sw-condition-is-always-valid',
        label: 'global.sw-condition.condition.alwaysValidRule',
        scopes: ['global'],
        group: 'general',
    });
    ruleConditionService.addCondition('dateRange', {
        component: 'sw-condition-date-range',
        label: 'global.sw-condition.condition.dateRangeRule.label',
        scopes: ['global'],
        group: 'general',
    });
    ruleConditionService.addCondition('timeRange', {
        component: 'sw-condition-time-range',
        label: 'global.sw-condition.condition.timeRangeRule',
        scopes: ['global'],
        group: 'general',
    });

    ruleConditionService.addCondition('heptacomAdminOpenAuthEmail', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.email',
        scopes: ['global'],
        group: 'user',
    });
    ruleConditionService.addCondition('heptacomAdminOpenAuthTimeZone', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.timeZone',
        scopes: ['jira', 'jumpcloud', 'keycloak', 'okta', 'open_id_connect', 'saml2'],
        group: 'user',
    });
    ruleConditionService.addCondition('heptacomAdminOpenAuthLocale', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.locale',
        scopes: ['google_cloud', 'jumpcloud', 'keycloak', 'okta', 'onelogin', 'open_id_connect', 'saml2'],
        group: 'user',
    });
    ruleConditionService.addCondition('heptacomAdminOpenAuthPrimaryKey', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.primaryKey',
        scopes: ['global'],
        group: 'user',
    });

    return ruleConditionService;
};
