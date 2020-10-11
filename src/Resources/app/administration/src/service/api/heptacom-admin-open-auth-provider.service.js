const { Classes } = Shopware;
const { ApiService } = Classes;

class HeptacomAdminOpenAuthProviderApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'heptacom_admin_open_auth_provider') {
        super(httpClient, loginService, apiEndpoint);
    }

    list() {
        const headers = this.getBasicHeaders();

        return this.httpClient
            .get(`_action/${this.getApiBasePath()}/list`, { headers })
            .then(response => ApiService.handleResponse(response));
    }

    factorize(providerKey) {
        const headers = this.getBasicHeaders();

        return this.httpClient
            .post(`_action/${this.getApiBasePath()}/factorize`, { provider_key: providerKey }, { headers })
            .then(response => ApiService.handleResponse(response));
    }

    getRedirectUri(clientId) {
        const headers = this.getBasicHeaders();

        return this.httpClient
            .post(`_action/${this.getApiBasePath()}/client-redirect-url`, { client_id: clientId }, { headers })
            .then(response => ApiService.handleResponse(response));
    }
}

export default HeptacomAdminOpenAuthProviderApiService;
