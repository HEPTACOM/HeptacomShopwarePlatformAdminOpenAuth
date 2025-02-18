import HeptacomAdminOpenAuthProviderApiService from '../service/api/heptacom-admin-open-auth-provider.service';
import HeptacomAdminOpenAuthRuleActionsApiService from '../service/api/heptacom-admin-open-auth-rule-actions.service';
import {HeptacomOauthRuleDataProviderService} from '../service/heptacom-oauth-rule-data-provider.service';
import dataProvider from '../service/condition-type-data-provider.decorator';

const { Application } = Shopware;

Application
    .addServiceProvider('HeptacomAdminOpenAuthProviderApiService', container => {
        const initContainer = Application.getContainer('init');
        return new HeptacomAdminOpenAuthProviderApiService(initContainer.httpClient, container.loginService);
    })
    .addServiceProvider('HeptacomAdminOpenAuthRuleActionsApiService', container => {
        const initContainer = Application.getContainer('init');
        return new HeptacomAdminOpenAuthRuleActionsApiService(initContainer.httpClient, container.loginService);
    })
    .addServiceProvider('heptacomOauthRuleDataProvider', () => {
        return new HeptacomOauthRuleDataProviderService();
    })
    .addServiceProviderDecorator('heptacomOauthRuleDataProvider', dataProvider);
