import HeptacomAdminOpenAuthProviderApiService from '../service/api/heptacom-admin-open-auth-provider.service';

const { Application } = Shopware;

Application.addServiceProvider('HeptacomAdminOpenAuthProviderApiService', container => {
    const initContainer = Application.getContainer('init');
    return new HeptacomAdminOpenAuthProviderApiService(initContainer.httpClient, container.loginService);
});
