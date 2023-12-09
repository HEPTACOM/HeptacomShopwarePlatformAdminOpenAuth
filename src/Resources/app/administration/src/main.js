import './app';
import './extension/sw-profile-index-general';
import './extension/sw-verify-user-modal';
import './module/heptacom-admin-open-auth-client';
import './provider';
import './init/services.init';
import {HeptacomOauthRuleDataProviderService} from './service/heptacom-oauth-rule-data-provider.service';
import dataProvider from './service/condition-type-data-provider.decorator';

Shopware.Application
    .addServiceProvider('heptacomOauthRuleDataProvider', () => {
        return new HeptacomOauthRuleDataProviderService();
    })
    .addServiceProviderDecorator('heptacomOauthRuleDataProvider', dataProvider);
