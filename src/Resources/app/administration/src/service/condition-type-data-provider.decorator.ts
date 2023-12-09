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

    ruleConditionService.addCondition('heptacomAdminOpenAuth.displayName', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.displayName',
        scopes: ['global'],
        group: 'user',
    });
    ruleConditionService.addCondition('heptacomAdminOpenAuth.firstName', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.firstName',
        scopes: ['global'],
        group: 'user',
    });
    ruleConditionService.addCondition('heptacomAdminOpenAuth.lastName', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.lastName',
        scopes: ['global'],
        group: 'user',
    });
    ruleConditionService.addCondition('heptacomAdminOpenAuth.emails', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.emails',
        scopes: ['global'],
        group: 'user',
    });
    ruleConditionService.addCondition('heptacomAdminOpenAuth.primaryEmail', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.primaryEmail',
        scopes: ['global'],
        group: 'user',
    });
    ruleConditionService.addCondition('heptacomAdminOpenAuth.timeZone', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.timeZone',
        scopes: ['global'],
        group: 'user',
    });
    ruleConditionService.addCondition('heptacomAdminOpenAuth.locale', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.locale',
        scopes: ['global'],
        group: 'user',
    });
    ruleConditionService.addCondition('heptacomAdminOpenAuth.primaryKey', {
        component: 'sw-condition-generic',
        label: 'heptacom-admin-open-auth.condition.rule.primaryKey',
        scopes: ['global'],
        group: 'user',
    });

    return ruleConditionService;
};
