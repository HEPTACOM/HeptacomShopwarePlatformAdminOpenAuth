const { Classes } = Shopware;
const { ApiService } = Classes;

class HeptacomAdminOpenAuthRuleActionsApiService extends ApiService {
    constructor(httpClient, loginService, apiEndpoint = 'heptacom_admin_open_auth_rule_actions') {
        super(httpClient, loginService, apiEndpoint);
    }

    list() {
        const headers = this.getBasicHeaders();

        return this.httpClient
            .get(`_action/${this.getApiBasePath()}/list`, { headers })
            .then(response => ApiService.handleResponse(response));
    }
}

export default HeptacomAdminOpenAuthRuleActionsApiService;
