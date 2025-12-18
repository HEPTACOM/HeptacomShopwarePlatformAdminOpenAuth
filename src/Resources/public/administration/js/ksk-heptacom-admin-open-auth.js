(()=>{var _t=Object.defineProperty;var o=(e,t)=>()=>(e&&(t=e(e=0)),t);var a=(e,t)=>{for(var i in t)_t(e,i,{get:t[i],enumerable:!0})};var k={};a(k,{default:()=>gt});var gt,C=o(()=>{gt={methods:{validateCurrentValue(e){let t=this.getURLInstance(e);return t?t.toString().replace(/([a-zA-Z0-9]+\:\/\/)+/,"").replace(t.host,this.unicodeUriFilter(t.host)):null}}}});var x=o(()=>{});var $,O=o(()=>{$=`{% block heptacom_admin_open_auth_user_confirm %}
    <div class="heptacom-admin-open-auth-user-confirm-login">
        <mt-loader v-if="loading"></mt-loader>
        <template v-else>
            <mt-loader v-if="waitingForConfirmation"></mt-loader>
            <sw-card-section
                class="heptacom-admin-open-auth-user-confirm-login--clients"
                :divider="sectionDivider"
                v-if="clients.length > 0"
            >
                <mt-banner
                    v-if="popupsAreBlocked"
                    variant="attention"
                >
                    {{ $tc('heptacom-admin-open-auth-user-confirm-login.popupsBlocked') }}
                </mt-banner>
                <template
                    v-for="client of clients"
                    :key="client.id"
                >
                    <mt-button
                        block
                        variant="secondary"
                        @click="startAuthFlow(client)"
                    >
                        {{ $t('heptacom-admin-open-auth-user-confirm-login.confirmWith', {
                            'clientName': client.name
                        }) }}
                        <mt-icon
                            name="regular-external-link-s"
                            size="16px"
                        ></mt-icon>
                    </mt-button>
                </template>
            </sw-card-section>
        </template>
    </div>
{% endblock %}
`});var P={};a(P,{default:()=>vt});var Oi,h,S,vt,F=o(()=>{x();O();({Component:Oi,Context:h}=Shopware),S="HeptacomAdminOpenAuthConfirmState",vt={template:$,inject:["loginService","repositoryFactory"],props:{divider:{type:Boolean,default:!0}},data(){return{loading:!0,clients:[],waitingForConfirmation:!1,confirmationClient:null,popupsAreBlocked:null}},computed:{heptacomAdminOpenAuthClientsRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client")},httpClient(){return this.heptacomAdminOpenAuthClientsRepository.httpClient},sectionDivider(){return this.divider?"bottom":""}},created(){this.createdComponent()},methods:{createdComponent(){this.loadClients()},loadClients(){this.loading=!0,this.clients=[],this.getConnectedClients().then(e=>{this.clients=e}).finally(()=>{this.loading=!1})},startAuthFlow(e){let t=this;this.waitingForConfirmation=!0,this.popupsAreBlocked=null,this.confirmationClient=e,localStorage.removeItem(S),this.getConfirmRedirectUrl(e.id).then(i=>{let n=Math.floor((screen.width-600)/2),r=Math.floor((screen.height-600)/2),s=window.open(i,this.$t("heptacom-admin-open-auth-user-confirm-login.confirmWith",{clientName:e.name}),`location=0,status=0,width=600,height=600, top=${r}, left=${n}`),d=null,_=window.setTimeout(()=>{(!s||s.closed||typeof s.closed>"u")&&(t.popupsAreBlocked=!0,t.waitingForConfirmation=!1,d&&window.clearInterval(d))},1200);try{s.focus(),t.popupsAreBlocked=!1}catch{t.popupsAreBlocked=!0,t.waitingForConfirmation=!1,window.clearTimeout(_);return}d=window.setInterval(()=>{if(!this.waitingForConfirmation){window.clearInterval(d),window.clearTimeout(_),this.popupsAreBlocked=!1,s.close();return}if(s.closed){window.clearInterval(d),window.clearTimeout(_),this.popupsAreBlocked=!1,this.waitingForConfirmation=!1;let g=localStorage.getItem(S);g&&this.verifyByState(JSON.parse(g).state)}},1e3)})},abortAuthFlow(){this.confirmationClient=null,this.waitingForConfirmation=!1},getConnectedClients(){let e=this.heptacomAdminOpenAuthClientsRepository.buildHeaders(h.api);return this.httpClient.get("/_admin/open-auth/client/list",{headers:e}).then(t=>t.data.data.filter(i=>i.connected))},getConfirmRedirectUrl(e){let t=this.heptacomAdminOpenAuthClientsRepository.buildHeaders(h.api);return this.httpClient.get(`/_admin/open-auth/${e}/confirm`,{headers:t}).then(i=>i.data.target)},verifyByState(e){this.httpClient.post("/oauth/token",{grant_type:"heptacom_admin_open_auth_one_time_token",client_id:"administration",scope:"user-verified",one_time_token:e},{baseURL:h.api.apiPath}).then(t=>{let i={...h.api};i.authToken.access=t.data.access_token;let n={...this.loginService.getBearerAuthentication(),access:i.authToken.access};this.loginService.setBearerAuthentication(n),this.$emit("confirm",i)})}}}});var L,U=o(()=>{L=`{% block heptacom_admin_open_auth_role_assignment_action_config %}
    <mt-switch
        :label="$t('heptacom-admin-open-auth-client.actions.heptacomAdminOpenAuthRoleAssignment.userBecomeAdmin')"
        v-model="rule.actionConfig.userBecomeAdmin"
    ></mt-switch>

    <sw-entity-multi-id-select
        v-if="!rule.actionConfig.userBecomeAdmin"
        :repository="aclRoleRepository"
        v-model:value="rule.actionConfig.aclRoleIds"
        :label="$t('heptacom-admin-open-auth-client.actions.heptacomAdminOpenAuthRoleAssignment.defaultAclRoles')"
    ></sw-entity-multi-id-select>
{% endblock %}
`});var T={};a(T,{default:()=>Ct});var Ct,D=o(()=>{U();Ct={template:L,inject:["repositoryFactory"],props:{client:{required:!0,type:Object},rule:{required:!0,type:Object}},computed:{aclRoleRepository(){return this.repositoryFactory.create("acl_role")}}}});var E,j=o(()=>{E=`{% block heptacom_admin_open_auth_client_rule_container %}
    <div>
        <mt-loader v-if="isLoading"></mt-loader>
        <template v-else>
            <template
                v-for="rule in sortedRules"
                :key="rule.id"
            >
                <heptacom-admin-open-auth-client-rule-item
                    :rule="rule"
                    :client="client"
                    :action="action"
                    @conditions-deleted="conditionsDeleted"
                    @move-up="moveRuleUp(rule)"
                    @move-down="moveRuleDown(rule)"
                    @delete="deleteRule(rule)"
                ></heptacom-admin-open-auth-client-rule-item>
            </template>
            <mt-banner
                v-if="sortedRules.length === 0"
                variant="attention"
            >
                {{ $t('heptacom-admin-open-auth-client.components.rule-container.warningEmpty') }}
            </mt-banner>
            <mt-button
                block
                @click="addRule"
                variant="secondary"
            >
                {{ $t('global.default.add') }}
            </mt-button>
        </template>
    </div>
{% endblock %}
`});var I={};a(I,{default:()=>Ot});var Ot,H=o(()=>{j();Ot={template:E,inject:["repositoryFactory","ruleConditionsConfigApiService"],props:{client:{required:!0,type:Object},action:{required:!0,type:Array}},data(){return{isLoading:!1}},computed:{ruleRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client_rule")},sortedRules(){return this.client.rules.filter(e=>e.actionName===this.action.name).sort((e,t)=>e.position-t.position)}},created(){this.createdComponent().then()},methods:{async createdComponent(){this.isLoading=!0,await this.loadConditionData(),this.isLoading=!1},async loadConditionData(){await this.ruleConditionsConfigApiService.load()},addRule(){let e=this.ruleRepository.create();e.clientId=this.client.id,e.actionName=this.action.name,e.actionConfig={},e.position=this.sortedRules.length,this.client.rules.add(e)},conditionsDeleted(e){this.$emit("conditions-deleted",e)},moveRuleUp(e){this.swapRules(e.position,e.position-1)},moveRuleDown(e){this.swapRules(e.position,e.position+1)},deleteRule(e){this.$emit("rule-deleted",e.id),this.client.rules.remove(e.id);let t=0;for(let i of this.sortedRules)i.position=t,t++},swapRules(e,t){let i=null,n=null;for(let r of this.sortedRules){if(i===null&&r.position===e){i=r;continue}if(n===null&&r.position===t){n=r;continue}}i===null||n===null||(i.position=t,n.position=e)}}}});var N=o(()=>{});var z,B=o(()=>{z=`{% block heptacom_admin_open_auth_client_rule_item %}
    <sw-collapse
        expand-on-loading
        class="heptacom-admin-open-auth-rule-item__collapse"
    >
        <template #header="{ expanded }">
            <div class="heptacom-admin-open-auth-rule-item__collapse-header">
                <h4
                    class="heptacom-admin-open-auth-rule-item__collapse-header-title"
                >
                    {{ $tc('heptacom-admin-open-auth-client.components.rule-item.title', rule.position + 1) }}
                </h4>
                <mt-icon
                    v-if="expanded"
                    class="heptacom-admin-open-auth-rule-item__collapse-header-collapsed-indicator"
                    name="regular-chevron-down-xxs"
                    size="12px"
                />
                <mt-icon
                    v-else
                    class="heptacom-admin-open-auth-rule-item__collapse-header-collapsed-indicator"
                    name="regular-chevron-right-xxs"
                    size="12px"
                />
            </div>
        </template>

        <template #content>
            <sw-condition-tree
                v-if="rule && conditionRepository"
                class="heptacom-admin-open-auth-rule-item__condition-tree"
                association-field="clientRuleId" {# the field that is used for assigning the rule to its parent #}
                :scopes="scopes"
                :initial-conditions="rule.conditions"
                :condition-repository="conditionRepository" {# any entity repository that has children and the above specified association field #}
                :condition-data-provider-service="heptacomOauthRuleDataProvider"
                :association-value="rule.id" {# the association id (so in this case the ruleId) #}
                :root-condition="null"
                @conditions-changed="onConditionsChanged"
            />
            <mt-switch
                :label="$t('heptacom-admin-open-auth-client.components.rule-item.stopOnMatch')"
                v-model="rule.stopOnMatch"
            ></mt-switch>
            <component
                v-if="rule && action"
                :is="action.component"
                v-bind="actionConfigProps"
            ></component>
            <div class="heptacom-admin-open-auth-rule-item__collapse-footer">
                <mt-button
                    block
                    variant="secondary"
                    @click="onMoveUp"
                >
                    <template #iconFront>
                        <mt-icon
                            name="regular-chevron-up-xxs"
                            size="12px"
                        />
                    </template>
                    {{ $tc('heptacom-admin-open-auth-client.components.rule-item.moveUp') }}
                </mt-button>
                <mt-button
                    block
                    variant="critical"
                    @click="onDelete"
                >
                    {{ $tc('global.default.delete') }}
                </mt-button>
                <mt-button
                    block
                    variant="secondary"
                    @click="onMoveDown"
                >
                    {{ $tc('heptacom-admin-open-auth-client.components.rule-item.moveDown') }}
                    <template #iconBack>
                        <mt-icon
                            name="regular-chevron-down-xxs"
                            size="12px"
                        />
                    </template>
                </mt-button>
            </div>
        </template>
    </sw-collapse>
{% endblock %}
`});var K={};a(K,{default:()=>St});var St,X=o(()=>{N();B();St={template:z,inject:["repositoryFactory","heptacomOauthRuleDataProvider"],props:{isLoading:{required:!1,type:Boolean,default:!1},client:{required:!0,type:Object},rule:{required:!0,type:Object},action:{required:!0,type:Object}},computed:{conditionRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client_rule_condition")},scopes(){return["global",this.client.provider]},actionConfigProps(){return{rule:this.rule,client:this.client}}},methods:{onConditionsChanged({deletedIds:e}){e.length>0&&this.$emit("conditions-deleted",e)},onMoveUp(){this.$emit("move-up",{})},onMoveDown(){this.$emit("move-down",{})},onDelete(){this.$emit("delete",{})}}}});var J=o(()=>{});var W,G=o(()=>{W=`{% block heptacom_admin_open_auth_client_create_page %}
    <sw-page
        :showSearchBar="false"
        class="heptacom-admin-open-auth-client-create-page"
    >
        {% block heptacom_admin_open_auth_client_create_page_inner %}{% endblock %}

        {% block heptacom_admin_open_auth_client_create_page_smart_bar_header %}
        <template #smart-bar-header>
            <h2>
                {{ $t('heptacom-admin-open-auth-client.pages.create.header') }}
            </h2>
        </template>
        {% endblock heptacom_admin_open_auth_client_create_page_smart_bar_header %}

        {% block heptacom_admin_open_auth_client_create_page_content %}
        <template #content>
            <template v-if="isLoading">
                {% block heptacom_admin_open_auth_client_create_page_content_loader %}
                <mt-loader></mt-loader>
                {% endblock %}
            </template>

            <template v-else>
                {% block heptacom_admin_open_auth_client_create_page_content_providers %}
                <sw-card-view class="heptacom-admin-open-auth-client-create_card-view">
                    {% block heptacom_admin_open_auth_client_create_page_content_providers_list %}
                    <template
                        v-for="provider of items"
                        :key="provider.key"
                    >
                        <mt-card
                            :title="provider.label"
                            :classes="provider.classes"
                            :position-identifier="'heptacom-admin-open-auth-client-create-provider-' + provider.key"
                        >
                            {% block heptacom_admin_open_auth_client_create_page_content_providers_list_item %}
                            {% block heptacom_admin_open_auth_client_create_page_content_providers_list_item_logo %}
                            <div class="logo">
                                <img
                                    :src="assetFilter('/kskheptacomadminopenauth/static/logo/'+provider.logoFile)"
                                    :alt="provider.label"
                                />
                            </div>
                            {% endblock %}

                            {% block heptacom_admin_open_auth_client_create_page_content_providers_list_item_action %}
                            <mt-button
                                @click="createClient(provider)"
                                class="heptacom-admin-open-auth-client-create-page-providers-provider--action"
                                block
                                variant="secondary"
                            >
                                {{ provider.actionLabel }}
                            </mt-button>
                            {% endblock %}
                            {% endblock %}
                        </mt-card>
                    </template>
                    {% endblock %}
                </sw-card-view>
                {% endblock %}
            </template>
        </template>
        {% endblock %}
    </sw-page>
{% endblock %}
`});var Z={};a(Z,{default:()=>Ft});var Ft,Q=o(()=>{J();G();Ft={template:W,inject:["HeptacomAdminOpenAuthProviderApiService"],data(){return{isLoading:!0,items:null}},created(){this.loadData()},computed:{assetFilter(){return Shopware.Filter.getByName("asset")}},methods:{loadData(){this.isLoading=!0,this.loadProviders().then(()=>{this.isLoading=!1})},loadProviders(){return this.items=[],this.HeptacomAdminOpenAuthProviderApiService.list().then(e=>{this.items=e.data.map(t=>({key:t,label:this.$t(`heptacomAdminOpenAuthClient.providers.${t}.label`),logoFile:this.$t(`heptacomAdminOpenAuthClient.providers.${t}.logoFile`),actionLabel:this.$te(`.heptacomAdminOpenAuthClient.providersCreate.${t}`)?this.$t(`heptacomAdminOpenAuthClient.providersCreate.${t}`):this.$t("heptacom-admin-open-auth-client.pages.create.actions.create"),classes:["heptacom-admin-open-auth-client-create-page-providers-provider",`heptacom-admin-open-auth-client-create-page-providers--provider-${t}`]})).sort((t,i)=>t.label.localeCompare(i.label)),this.isLoading=!1})},createClient(e){return this.HeptacomAdminOpenAuthProviderApiService.factorize(e.key).then(t=>{this.$router.push({name:"heptacom.admin.open.auth.client.edit",params:{id:t.data.id}})})}}}});var Y=o(()=>{});var ee,V=o(()=>{ee=`{% block heptacom_admin_open_auth_client_edit_page %}
    <sw-page
        :showSearchBar="false"
        class="heptacom-admin-open-auth-client-edit-page"
    >
        {% block heptacom_admin_open_auth_client_edit_page_inner %}
        {% endblock %}

        {% block heptacom_admin_open_auth_client_edit_page_smart_bar_header %}
        <template #smart-bar-header>
            {{ dynamicName }}
        </template>
        {% endblock %}

        {% block heptacom_admin_open_auth_client_edit_page_search_bar_actions %}
        <template #smart-bar-actions>
            {% block heptacom_admin_open_auth_client_edit_page_search_bar_actions_help %}
            <mt-button
                variant="secondary"
                :disabled="isLoading"
                :isLoading="isLoading"
                :link="item ? $t('heptacomAdminOpenAuthClient.providers.' + item.provider + '.helpUrl') : ''"
            >
                <template #iconFront>
                    <mt-icon
                        name="regular-external-link"
                        size="16px"
                    ></mt-icon>
                </template>
                {{ $t('heptacom-admin-open-auth-client.pages.edit.actions.help') }}
            </mt-button>
            {% endblock %}

            {% block heptacom_admin_open_auth_client_edit_page_search_bar_actions_delete %}
            <mt-button
                v-if="acl.can('heptacom_admin_open_auth_client.deleter')"
                :disabled="isLoading"
                @click="showDeleteModal = true"
                variant="critical"
            >
                {{ $t('heptacom-admin-open-auth-client.pages.edit.actions.delete') }}
            </mt-button>
            {% endblock %}

            {% block heptacom_admin_open_auth_client_edit_page_search_bar_actions_cancel %}
            <mt-button
                :disabled="isLoading"
                variant="secondary"
                @click="cancelEdit"
            >
                {{ $t('heptacom-admin-open-auth-client.pages.edit.actions.cancel') }}
            </mt-button>
            {% endblock %}

            {% block heptacom_admin_open_auth_client_edit_page_search_bar_actions_save %}
            <sw-button-process
                :disabled="isLoading"
                :isLoading="isLoading"
                @click.prevent="saveItem"
                v-model:value="isSaveSuccessful"
                variant="primary"
            >
                {{ $t('heptacom-admin-open-auth-client.pages.edit.actions.save') }}
            </sw-button-process>
            {% endblock %}
        </template>
        {% endblock %}

        {% block heptacom_admin_open_auth_client_edit_page_content %}
        <template #content>
            <sw-card-view>
                {% block heptacom_admin_open_auth_client_edit_page_content_base_settings %}
                <mt-card
                    :isLoading="isLoading"
                    position-identifier="heptacom-admin-open-auth-client-edit-page-base-settings"
                >
                    <template v-if="item">
                        {% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner %}
                        {% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_name %}
                        <mt-text-field
                            :label="$t('heptacom-admin-open-auth-client.pages.edit.fields.name')"
                            v-model="item.name"
                        ></mt-text-field>
                        {% endblock %}
                        {% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_active %}
                        <mt-switch
                            :label="$t('heptacom-admin-open-auth-client.pages.edit.fields.active')"
                            v-model="item.active"
                        ></mt-switch>
                        {% endblock %}
                        {% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_can_login %}
                        <mt-switch
                            :disabled="!item.active"
                            :label="$t('heptacom-admin-open-auth-client.pages.edit.fields.login')"
                            v-model="item.login"
                        ></mt-switch>
                        {% endblock %}
                        {% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_can_connect %}
                        <mt-switch
                            :disabled="!item.active"
                            :label="$t('heptacom-admin-open-auth-client.pages.edit.fields.connect')"
                            v-model="item.connect"
                        ></mt-switch>
                        {% endblock %}
                        {% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_store_user_token %}
                        <mt-switch
                            :disabled="!item.active"
                            :label="$t('heptacom-admin-open-auth-client.pages.edit.fields.storeUserToken')"
                            v-model="item.storeUserToken"
                        ></mt-switch>
                        {% endblock %}
                        {% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_keep_user_updated %}
                        <mt-switch
                            :disabled="!item.active"
                            :label="$t('heptacom-admin-open-auth-client.pages.edit.fields.keepUserUpdated')"
                            :helpText="$t('heptacom-admin-open-auth-client.pages.edit.fields.keepUserUpdatedHelpText')"
                            v-model="item.keepUserUpdated"
                        ></mt-switch>
                        {% endblock %}
                        {% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_redirect_uri %}
                        <mt-text-field
                            v-if="redirectUri"
                            copyable
                            copyableTooltip
                            :label="$t('heptacom-admin-open-auth-client.pages.edit.fields.redirectUri')"
                            v-model="redirectUri"
                            disabled
                        ></mt-text-field>
                        {% endblock %}
                        {% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_metadata_uri %}
                        <mt-text-field
                            v-if="metadataUri"
                            :copyable="true"
                            :copyableTooltip="true"
                            :label="$t('heptacom-admin-open-auth-client.pages.edit.fields.metadataUri')"
                            v-model="metadataUri"
                            disabled
                        ></mt-text-field>
                        <mt-button
                            v-if="metadataUri"
                            class="sw-button__metadata-download"
                            :disabled="!metadataUri"
                            :link="metadataUri"
                            size="small"
                            block
                            variant="secondary"
                        >
                            {{ $t('heptacom-admin-open-auth-client.pages.edit.fields.downloadMetadata') }}
                        </mt-button>
                        {% endblock %}
                        {% endblock %}
                    </template>
                </mt-card>
                {% endblock %}

                {% block heptacom_admin_open_auth_client_edit_page_content_actions %}
                <mt-card
                    :isLoading="isLoading"
                    :title="$t('heptacom-admin-open-auth-client.pages.edit.ruleActions.title')"
                    position-identifier="heptacom-admin-open-auth-client-edit-page-actions"
                >
                    <template v-if="item && defaultActionTab !== ''">
                        {% block heptacom_admin_open_auth_client_edit_page_content_actions_inner %}
                        <sw-tabs :default-item="defaultActionTab">
                            <template #default="{ active }">
                                <template
                                    v-for="action in actions"
                                    :key="action.name"
                                >
                                    <sw-tabs-item
                                        :active-tab="active"
                                        :name="action.name"
                                    >
                                        {{ $t('heptacom-admin-open-auth-client.actions.' + action.name + '.label') }}
                                    </sw-tabs-item>
                                </template>
                            </template>

                            <template #content="{ active }">
                                <heptacom-admin-open-auth-client-rule-container
                                    v-if="actions[active] !== undefined"
                                    :client="item"
                                    :action="actions[active]"
                                    @rule-deleted="onRuleDeleted"
                                    @conditions-deleted="onConditionsDeleted"
                                ></heptacom-admin-open-auth-client-rule-container>
                            </template>
                        </sw-tabs>
                        {% endblock %}
                    </template>
                </mt-card>
                {% endblock %}

                {% block heptacom_admin_open_auth_client_edit_page_content_provider_settings %}
                <component
                    v-if="item && item.provider"
                    :is="providerSettingsComponent"
                    v-bind="providerSettingsProps"
                ></component>
                {% endblock %}
            </sw-card-view>
            {% block heptacom_admin_open_auth_client_edit_page_content_modal_delete %}
            <sw-modal
                v-if="showDeleteModal"
                :title="$t('heptacom-admin-open-auth-client.pages.edit.modals.delete.title')"
                @modal-close="showDeleteModal = false"
                variant="small"
            >
                {% block heptacom_admin_open_auth_client_edit_page_content_modal_delete_confirm_text %}
                <p>
                    {{ $t('heptacom-admin-open-auth-client.pages.edit.modals.delete.warning') }}
                </p>
                <p>
                    <strong>{{ dynamicName }}</strong>
                </p>
                <p>
                    {{ $t('heptacom-admin-open-auth-client.pages.edit.modals.delete.explanation') }}
                </p>
                {% endblock %}

                {% block heptacom_admin_open_auth_client_edit_page_content_modal_delete_footer %}
                <template #modal-footer>
                    {% block heptacom_admin_open_auth_client_edit_page_content_modal_delete_abort %}
                    <mt-button
                        size="small"
                        @click="showDeleteModal = false"
                        variant="secondary"
                    >
                        {{ $t('heptacom-admin-open-auth-client.pages.edit.modals.delete.cancel') }}
                    </mt-button>
                    {% endblock %}

                                {% block heptacom_admin_open_auth_client_edit_page_content_modal_delete_delete %}
                    <mt-button
                        size="small"
                        variant="critical"
                        @click="onConfirmDelete"
                    >
                        {{ $t('heptacom-admin-open-auth-client.pages.edit.modals.delete.confirm') }}
                    </mt-button>
                    {% endblock %}
                </template>
                {% endblock %}
            </sw-modal>
            {% endblock %}
        </template>
        {% endblock %}
    </sw-page>
{% endblock %}
`});var oe={};a(oe,{default:()=>Mt});var p,te,qt,ie,Mt,ne=o(()=>{Y();V();({Context:p,Mixin:te,Data:qt}=Shopware),{Criteria:ie}=qt,Mt={template:ee,inject:["acl","repositoryFactory","HeptacomAdminOpenAuthProviderApiService","HeptacomAdminOpenAuthRuleActionsApiService"],mixins:[te.getByName("placeholder"),te.getByName("notification")],props:{clientId:{required:!0,type:String}},data(){return{isLoading:!0,isSaveSuccessful:!1,item:null,showDeleteModal:!1,redirectUri:null,metadataUri:null,actions:{},defaultActionTab:"",deletedRuleIds:[],deletedConditionIds:[]}},created(){this.loadData()},computed:{dynamicName(){return this.placeholder(this.item,"name",this.$t("heptacom-admin-open-auth-client.pages.edit.title"))},clientRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client")},ruleRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client_rule")},ruleConditionRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client_rule_condition")},clientCriteria(){let e=new ie;return e.getAssociation("rules").addSorting(ie.sort("position","ASC")),e.addAssociation("rules.conditions"),e},providerSlug(){return(this.item&&this.item.provider?this.item.provider:"").replace(/_/g,"-")},providerSettingsComponent(){return`heptacom-admin-open-auth-provider-${this.providerSlug}-settings`},providerSettingsProps(){return{item:this.item}}},methods:{loadData(){this.isLoading=!0,Promise.all([this.loadClient(),this.loadActions()]).finally(()=>{this.isLoading=!1})},async loadClient(){this.item=null,this.item=await this.clientRepository.get(this.clientId,p.api,this.clientCriteria),this.redirectUri=(await this.HeptacomAdminOpenAuthProviderApiService.getRedirectUri(this.item.id)).target,this.metadataUri=(await this.HeptacomAdminOpenAuthProviderApiService.getMetadataUri(this.item.id)).target},async loadActions(){this.actions={};let e=await this.HeptacomAdminOpenAuthRuleActionsApiService.list();for(let t of e)this.actions[t.name]=t;e.length>0&&(this.defaultActionTab=e[0].name)},cancelEdit(){this.$router.push({name:this.$route.meta.parentPath})},saveItem(){this.isLoading=!0,this.saveClient().then(this.syncDeletedConditions).then(this.syncDeletedRules).then(()=>(this.isSaveSuccessful=!0,this.loadData())).catch(e=>{let t=this.client.name;throw this.createNotificationError({title:this.$tc("global.notification.notificationSaveErrorTitle"),message:this.$tc("global.notification.notificationSaveErrorMessage",0,{entityName:t})}),e}).finally(()=>{this.isLoading=!1})},saveClient(){return this.clientRepository.save(this.item,p.api)},syncDeletedRules(){if(this.deletedRuleIds.length>0)return this.ruleRepository.syncDeleted(this.deletedRuleIds,p.api).then(()=>{this.deletedRuleIds=[]})},syncDeletedConditions(){if(this.deletedConditionIds.length>0)return this.ruleConditionRepository.syncDeleted(this.deletedConditionIds,p.api).then(()=>{this.deletedConditionIds=[]})},onRuleDeleted(e){this.deletedRuleIds.push(e)},onConditionsDeleted(e){this.deletedConditionIds=[...this.deletedConditionIds,...e]},onConfirmDelete(){return this.showDeleteModal=!1,this.isLoading=!0,this.clientRepository.delete(this.item.id,p.api).then(()=>{this.$router.push({name:"heptacom.admin.open.auth.client.settings"})}).catch(e=>{let t=this.client.name;throw this.createNotificationError({title:this.$tc("global.notification.notificationSaveErrorTitle"),message:this.$tc("global.notification.notificationSaveErrorMessage",0,{entityName:t})}),e}).finally(()=>{this.isLoading=!1})}}}});var re,ae=o(()=>{re=`{% block heptacom_admin_open_auth_client_listing_page %}
    <sw-page class="heptacom-admin-open-auth-client-listing-page">
        {% block heptacom_admin_open_auth_client_listing_page_inner %}
        {% endblock %}

        {% block heptacom_admin_open_auth_client_listing_page_search_bar %}
        <template #search-bar>
            <sw-search-bar
                :initialSearch="term"
                @search="onSearch"
                initialSearchType="heptacom_admin_open_auth_client"
            ></sw-search-bar>
        </template>
        {% endblock %}

        {% block heptacom_admin_open_auth_client_listing_page_search_bar_actions %}
        <template #smart-bar-actions>
            <mt-button
                v-if="acl.can('heptacom_admin_open_auth_client.creator')"
                @click="this.$router.push({ name: 'heptacom.admin.open.auth.client.create' })"
                variant="primary"
            >
                {{ $t('heptacom-admin-open-auth-client.pages.listing.actions.create') }}
            </mt-button>
        </template>
        {% endblock %}

        {% block heptacom_admin_open_auth_client_listing_page_content %}
        <template #content>
            {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing %}
            <sw-entity-listing
                v-if="items"
                :items="items"
                :repository="clientRepository"
                :showSelection="false"
                :columns="columns"
                :isLoading="!isLoading"
                :showActions="false"
            >
                {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing_inner %}
                        {% endblock %}

                        {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing_columns_active %}
                <template #column-active="{ item }">
                    <mt-icon
                        :color="getLoginColor(item)"
                        v-tooltip="{ message: $t('heptacom-admin-open-auth-client.pages.listing.iconTooltip.active') }"
                        name="regular-sign-in"
                        size="16px"
                    ></mt-icon>
                    <mt-icon
                        :color="getConnectColor(item)"
                        v-tooltip="{ message: $t('heptacom-admin-open-auth-client.pages.listing.iconTooltip.connection') }"
                        name="regular-share"
                        size="16px"
                    ></mt-icon>
                </template>
                {% endblock %}

                        {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing_columns_created_at %}
                <template #column-createdAt="{ item }">
                    {{ dateFilter(item.createdAt, { hour: '2-digit', minute: '2-digit' }) }}
                </template>
                {% endblock %}

                        {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing_columns_provider %}
                <template #column-provider="{ item }">
                    {{ $te('heptacomAdminOpenAuthClient.providers.' + item.provider + '.label') ? $t('heptacomAdminOpenAuthClient.providers.' + item.provider + '.label') : item.provider }}
                </template>
                {% endblock %}

                        {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing_pagination %}
                <template #pagination>
                    <sw-pagination
                        :page="page"
                        :limit="limit"
                        :total="total"
                        :total-visible="7"
                        @page-change="onPageChange"
                    ></sw-pagination>
                </template>
                {% endblock %}
            </sw-entity-listing>
            {% endblock %}
        </template>
        {% endblock %}

        {% block heptacom_admin_open_auth_client_listing_page_sidebar_container %}
        <template #sidebar>
            {% block heptacom_admin_open_auth_client_listing_page_sidebar %}
            <sw-sidebar>
                {% block heptacom_admin_open_auth_client_listing_page_sidebar_inner %}
                        {% endblock %}

                        {% block heptacom_admin_open_auth_client_listing_page_sidebar_refresh %}
                <sw-sidebar-item
                    :title="$tc('heptacom-admin-open-auth-client.pages.listing.actions.refresh')"
                    @click="onRefresh"
                    icon="regular-undo"
                ></sw-sidebar-item>
                {% endblock %}
            </sw-sidebar>
            {% endblock %}
        </template>
        {% endblock %}
    </sw-page>
{% endblock %}
`});var le=o(()=>{});var se={};a(se,{default:()=>jt});var Lt,Tt,Dt,v,jt,de=o(()=>{ae();le();({Context:Lt,Data:Tt,Mixin:Dt}=Shopware),{Criteria:v}=Tt,jt={template:re,inject:["acl","repositoryFactory"],mixins:[Dt.getByName("listing")],data(){return{isLoading:!0,items:null}},created(){this.getList()},computed:{clientRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client")},clientCriteria(){let e=new v,t=this.getMainListingParams();return e.addAssociation("userKeys"),e.setLimit(t.limit),e.setPage(t.page),e.addSorting(v.sort(t.sortBy||"name",t.sortDirection||"ASC")),t.term&&t.term.length&&e.addFilter(v.contains("name",t.term)),e},columns(){return[{property:"active",label:this.$t("heptacom-admin-open-auth-client.pages.listing.columns.active"),allowResize:!1,width:"50px"},{property:"name",label:this.$t("heptacom-admin-open-auth-client.pages.listing.columns.name"),routerLink:"heptacom.admin.open.auth.client.edit"},{property:"provider",label:this.$t("heptacom-admin-open-auth-client.pages.listing.columns.provider")},{property:"userKeys.length",label:this.$t("heptacom-admin-open-auth-client.pages.listing.columns.users"),width:"100px"},{property:"createdAt",label:this.$t("heptacom-admin-open-auth-client.pages.listing.columns.createdAt"),width:"200px"}]},dateFilter(){return Shopware.Filter.getByName("date")}},methods:{getList(){return this.loadData()},loadData(){this.isLoading=!0,this.loadClients().then(()=>{this.isLoading=!1})},loadClients(){return this.items=null,this.clientRepository.search(this.clientCriteria,Lt.api).then(e=>{this.items=e})},getLoginColor(e){return e.active?e.login?"#00cc00":"#cc0000":"#333333"},getConnectColor(e){return e.active?e.connect?"#00cc00":"#cc0000":"#333333"}}}});var ge,_e=o(()=>{ge=`{% block heptacom_admin_open_auth_provider_cidaas_settings %}
    <mt-card position-identifier="heptacom-admin-open-auth-provider-cidaas-settings">
        <heptacom-admin-open-auth-url-field
            required
            :omitUrlHash="true"
            :omitUrlSearch="true"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.cidaas.organizationUrl')"
            placeholder="my-company.cidaas.eu"
            v-model:value="item.config.organizationUrl"
        ></heptacom-admin-open-auth-url-field>
        <mt-text-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.cidaas.clientId')"
            v-model="item.config.clientId"
        ></mt-text-field>
        <mt-password-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.cidaas.clientSecret')"
            v-model="item.config.clientSecret"
        ></mt-password-field>
        <sw-tagged-field
            :label="$t('heptacomAdminOpenAuthClient.providerFields.cidaas.additionalScopes')"
            v-model:value="item.config.scopes"
        ></sw-tagged-field>
    </mt-card>
{% endblock %}
`});var fe={};a(fe,{default:()=>Kt});var Kt,ve=o(()=>{_e();Kt={template:ge,props:{item:{required:!0}},watch:{item(e){e.config.scopes||(e.config.scopes=[])}}}});var Ae,be=o(()=>{Ae=`{% block heptacom_admin_open_auth_client_google_cloud_settings %}
    <mt-card
        position-identifier="heptacom-admin-open-auth-provider-google-cloud-settings"
    >
        <mt-text-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.google_cloud.clientId')"
            v-model="item.config.clientId"
        ></mt-text-field>
        <mt-password-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.google_cloud.clientSecret')"
            v-model="item.config.clientSecret"
        ></mt-password-field>
        <sw-tagged-field
            :label="$t('heptacomAdminOpenAuthClient.providerFields.google_cloud.additionalScopes')"
            v-model:value="item.config.scopes"
        ></sw-tagged-field>
    </mt-card>
{% endblock %}
`});var we={};a(we,{default:()=>Jt});var Jt,ye=o(()=>{be();Jt={template:Ae,props:{item:{required:!0}},watch:{item(e){e.config.scopes||(e.config.scopes=[])}}}});var Ce,ke=o(()=>{Ce=`{% block heptacom_admin_open_auth_provider_jira_settings %}
    <mt-card position-identifier="heptacom-admin-open-auth-provider-jira-settings">
        <mt-text-field
            :label="$t('heptacomAdminOpenAuthClient.providerFields.jira.clientId')"
            v-model="item.config.clientId"
        ></mt-text-field>
        <mt-password-field
            :label="$t('heptacomAdminOpenAuthClient.providerFields.jira.clientSecret')"
            v-model="item.config.clientSecret"
        ></mt-password-field>
        <sw-tagged-field
            :label="$t('heptacomAdminOpenAuthClient.providerFields.jira.additionalScopes')"
            v-model:value="item.config.scopes"
        ></sw-tagged-field>
    </mt-card>
{% endblock %}
`});var xe={};a(xe,{default:()=>Wt});var Wt,Oe=o(()=>{ke();Wt={template:Ce,props:{item:{required:!0}}}});var Se,$e=o(()=>{Se=`{% block heptacom_admin_open_auth_provider_jumpcloud_settings %}
    {% block heptacom_admin_open_auth_provider_jumpcloud_settings_idp %}
        <mt-card
            :title="$t('heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderCardTitle')"
            position-identifier="heptacom-admin-open-auth-provider-jumpcloud-settings-idp"
        >
            <!-- auto config -->
            <mt-textarea
                :disabled="!!item.config.identityProviderMetadataUrl"
                :label="$t('heptacomAdminOpenAuthClient.providerFields.jumpcloud.identityProviderMetadataXml')"
                v-model="item.config.identityProviderMetadataXml"
            ></mt-textarea>
        </mt-card>
    {% endblock %}

    {% block heptacom_admin_open_auth_provider_jumpcloud_settings_attribute_mapping %}
        <mt-card
            :title="$t('heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.cardTitle')"
            position-identifier="heptacom-admin-open-auth-provider-jumpcloud-settings-attribute-mapping"
        >
            {% block heptacom_admin_open_auth_provider_jumpcloud_settings_attribute_mapping_inner %}
            <template v-for="mappedProperty of availableProperties">
                <mt-text-field
                    :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.field.' + mappedProperty)"
                    v-model="item.config.attributeMapping[mappedProperty]"
                ></mt-text-field>
            </template>
            {% endblock %}
        </mt-card>
    {% endblock %}
{% endblock %}
`});var Pe={};a(Pe,{default:()=>Qt});var Qt,Fe=o(()=>{$e();Qt={template:Se,props:{item:{required:!0}},data(){return{availableProperties:["firstName","lastName","email","timezone","locale","roles"]}},watch:{item(e){e.config.attributeMapping||(e.config.attributeMapping={})}}}});var qe,Re=o(()=>{qe=`{% block heptacom_admin_open_auth_provider_keycloak_settings %}
    <mt-card
        position-identifier="heptacom-admin-open-auth-provider-keycloak-settings"
    >
        <mt-textarea
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.keycloak.keycloakOidcJson')"
            :placeholder="jsonPlaceholder"
            v-model="item.config.keycloakOidcJson"
        ></mt-textarea>
        <sw-tagged-field
            :label="$t('heptacomAdminOpenAuthClient.providerFields.keycloak.additionalScopes')"
            v-model:value="item.config.scopes"
        ></sw-tagged-field>
    </mt-card>
{% endblock %}
`});var Me={};a(Me,{default:()=>Vt});var Vt,Ue=o(()=>{Re();Vt={template:qe,props:{item:{required:!0}},data(){return{jsonPlaceholder:JSON.stringify({realm:"master","auth-server-url":"https://keycloak.my-company.com/auth/","ssl-required":"external",resource:"my-client",credentials:{secret:"abcdefghijgklmnopqrstuvwxyz"},"confidential-port":0},null,"	")}},watch:{item(e){e.config.scopes||(e.config.scopes=[])}}}});var Te,Le=o(()=>{Te=`{% block heptacom_admin_open_auth_provider_microsoft_entra_id_oidc_settings %}
    <mt-card
        position-identifier="heptacom-admin-open-auth-provider-microsoft-entra-id-oidc-settings"
    >
        <mt-text-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.microsoft_entra_id_oidc.tenantId')"
            v-model="item.config.tenantId"
        ></mt-text-field>
        <mt-text-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.microsoft_entra_id_oidc.clientId')"
            v-model="item.config.clientId"
        ></mt-text-field>
        <mt-password-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.microsoft_entra_id_oidc.clientSecret')"
            v-model="item.config.clientSecret"
        ></mt-password-field>
        <sw-tagged-field
            :label="$t('heptacomAdminOpenAuthClient.providerFields.microsoft_entra_id_oidc.additionalScopes')"
            v-model:value="item.config.scopes"
        ></sw-tagged-field>
    </mt-card>
{% endblock %}
`});var De={};a(De,{default:()=>ti});var ti,je=o(()=>{Le();ti={template:Te,props:{item:{required:!0}},watch:{item(e){e.config.scopes||(e.config.scopes=[])}}}});var Ie,Ee=o(()=>{Ie=`{% block heptacom_admin_open_auth_provider_okta_settings %}
    <mt-card position-identifier="heptacom-admin-open-auth-provider-okta-settings">
        <heptacom-admin-open-auth-url-field
            required
            :omitUrlHash="true"
            :omitUrlSearch="true"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.okta.organizationUrl')"
            placeholder="my-company.okta.com"
            v-model:value="item.config.organizationUrl"
        ></heptacom-admin-open-auth-url-field>
        <mt-text-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.okta.clientId')"
            v-model="item.config.clientId"
        ></mt-text-field>
        <mt-password-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.okta.clientSecret')"
            v-model="item.config.clientSecret"
        ></mt-password-field>
        <sw-tagged-field
            :label="$t('heptacomAdminOpenAuthClient.providerFields.okta.additionalScopes')"
            v-model:value="item.config.scopes"
        ></sw-tagged-field>
    </mt-card>
{% endblock %}
`});var He={};a(He,{default:()=>oi});var oi,Ne=o(()=>{Ee();oi={template:Ie,props:{item:{required:!0}},watch:{item(e){e.config.scopes||(e.config.scopes=[])}}}});var ze,Be=o(()=>{ze=`{% block heptacom_admin_open_auth_provider_onelogin_settings %}
    <mt-card
        position-identifier="heptacom-admin-open-auth-provider-onelogin-settings"
    >
        <heptacom-admin-open-auth-url-field
            required
            :omitUrlHash="true"
            :omitUrlSearch="true"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.onelogin.organizationUrl')"
            placeholder="my-company.onelogin.com"
            v-model:value="item.config.organizationUrl"
        ></heptacom-admin-open-auth-url-field>
        <mt-text-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.onelogin.clientId')"
            v-model="item.config.clientId"
        ></mt-text-field>
        <mt-password-field
            required
            :label="$t('heptacomAdminOpenAuthClient.providerFields.onelogin.clientSecret')"
            v-model="item.config.clientSecret"
        ></mt-password-field>
        <sw-tagged-field
            :label="$t('heptacomAdminOpenAuthClient.providerFields.onelogin.additionalScopes')"
            v-model:value="item.config.scopes"
        ></sw-tagged-field>
    </mt-card>
{% endblock %}
`});var Ke={};a(Ke,{default:()=>ai});var ai,Xe=o(()=>{Be();ai={template:ze,props:{item:{required:!0}},watch:{item(e){e.config.scopes||(e.config.scopes=[])}}}});var Ge,Je=o(()=>{Ge=`{% block sw_condition_value_content %}
    <div
        class="heptacom-admin-open-auth-condition-authenticated-request sw-condition__condition-value"
    >
        {% block sw_condition_authenticated_request_field_redirect_url %}
        <heptacom-admin-open-auth-url-field
            v-model:value="requestUrl"
            placeholder="my-company.my-idp.com/api/v1/profile/groups"
            size="medium"
            omitUrlHash
            required
            :disabled="disabled"
        />
        {% endblock %}

        {% block sw_condition_authenticated_request_field_jmes_path_expression %}
        <mt-text-field
            v-model="jmesPathExpression"
            placeholder="JMESPath query"
            size="default"
            required
            :disabled="disabled"
        />
        <mt-external-link
            small
            class="heptacom-admin-open-auth-condition-authenticated-request--documentation-link"
            href="https://jmespath.org/"
        >
            {{ $tc('heptacomAdminOpenAuthClient.providerFields.open_id_connect.condition.rule.jmesPathDocumentation') }}
        </mt-external-link>
        {% endblock %}
    </div>
{% endblock %}
`});var We=o(()=>{});var Ze={};a(Ze,{default:()=>di});var li,si,di,Qe=o(()=>{Je();We();({Component:li}=Shopware),{mapPropertyErrors:si}=li.getComponentHelper(),di={template:Ge,computed:{requestUrl:{get(){return this.ensureValueExist(),this.condition.value.requestUrl||null},set(e){this.ensureValueExist(),this.condition.value={...this.condition.value,requestUrl:e}}},jmesPathExpression:{get(){return this.ensureValueExist(),this.condition.value.jmesPathExpression||null},set(e){this.ensureValueExist(),this.condition.value={...this.condition.value,jmesPathExpression:e}}},...si("condition",["value.requestUrl","value.jmesPathExpression"]),currentError(){return this.conditionValueRequestUrlError||this.conditionValueJmesPathExpressionError}}}});var Ve,Ye=o(()=>{Ve=`{% block sw_condition_value_content %}
    <div
        class="heptacom-admin-open-auth-condition-id-token sw-condition__condition-value"
    >
        {% block sw_condition_id_token_field_jmes_path_expression %}
        <mt-text-field
            v-model="jmesPathExpression"
            placeholder="JMESPath query"
            size="default"
            required
            :disabled="disabled"
        />
        <mt-external-link
            small
            class="heptacom-admin-open-auth-condition-id-token--documentation-link"
            href="https://jmespath.org/"
        >
            {{ $tc('heptacomAdminOpenAuthClient.providerFields.open_id_connect.condition.rule.jmesPathDocumentation') }}
        </mt-external-link>
        {% endblock %}
    </div>
{% endblock %}
`});var et=o(()=>{});var tt={};a(tt,{default:()=>hi});var pi,mi,hi,it=o(()=>{Ye();et();({Component:pi}=Shopware),{mapPropertyErrors:mi}=pi.getComponentHelper(),hi={template:Ve,computed:{jmesPathExpression:{get(){return this.ensureValueExist(),this.condition.value.jmesPathExpression||null},set(e){this.ensureValueExist(),this.condition.value={...this.condition.value,jmesPathExpression:e}}},...mi("condition",["value.jmesPathExpression"]),currentError(){return this.conditionValueJmesPathExpressionError}}}});var nt,ot=o(()=>{nt=`{% block heptacom_admin_open_auth_provider_open_id_connect_settings %}
    <mt-card
        position-identifier="heptacom-admin-open-auth-provider-open-id-connect-settings"
    >
        <heptacom-admin-open-auth-url-field
            :omitUrlHash="true"
            :omitUrlSearch="true"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.open_id_connect.discoveryDocumentUrl')"
            :help-text="$t('heptacomAdminOpenAuthClient.providerFields.open_id_connect.discoveryDocumentUrl-help')"
            :placeholder="$t('heptacomAdminOpenAuthClient.providerFields.open_id_connect.discoveryDocumentUrl-placeholder')"
            v-model:value="item.config.discoveryDocumentUrl"
        ></heptacom-admin-open-auth-url-field>
        <heptacom-admin-open-auth-url-field
            :omitUrlHash="true"
            :omitUrlSearch="true"
            :disabled="!!item.config.discoveryDocumentUrl"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.open_id_connect.authorization_endpoint')"
            v-model:value="item.config.authorization_endpoint"
        ></heptacom-admin-open-auth-url-field>
        <heptacom-admin-open-auth-url-field
            :omitUrlHash="true"
            :omitUrlSearch="true"
            :disabled="!!item.config.discoveryDocumentUrl"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.open_id_connect.token_endpoint')"
            v-model:value="item.config.token_endpoint"
        ></heptacom-admin-open-auth-url-field>
        <heptacom-admin-open-auth-url-field
            :omitUrlHash="true"
            :omitUrlSearch="true"
            :disabled="!!item.config.discoveryDocumentUrl"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.open_id_connect.userinfo_endpoint')"
            v-model:value="item.config.userinfo_endpoint"
        ></heptacom-admin-open-auth-url-field>
        <mt-text-field
            :required="true"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.open_id_connect.clientId')"
            v-model="item.config.clientId"
        ></mt-text-field>
        <mt-password-field
            :required="true"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.open_id_connect.clientSecret')"
            v-model="item.config.clientSecret"
        ></mt-password-field>
        <sw-tagged-field
            :label="$t('heptacomAdminOpenAuthClient.providerFields.open_id_connect.additionalScopes')"
            v-model:value="item.config.scopes"
        ></sw-tagged-field>
    </mt-card>
{% endblock %}
`});var at={};a(at,{default:()=>_i});var _i,rt=o(()=>{ot();_i={template:nt,props:{item:{required:!0}},watch:{item(e){e.config.scopes||(e.config.scopes=[])}}}});var st,lt=o(()=>{st=`{% block heptacom_admin_open_auth_provider_saml2_settings %}
    <div>
        {% block heptacom_admin_open_auth_provider_saml2_settings_idp %}
        <mt-card
            :title="$t('heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderCardTitle')"
            position-identifier="heptacom-admin-open-auth-provider-saml2-settings-idp"
        >
            <!-- auto config -->
            <heptacom-admin-open-auth-url-field
                :omitUrlHash="true"
                :omitUrlSearch="false"
                :disabled="!!item.config.identityProviderMetadataXml"
                :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderMetadataUrl')"
                :help-text="$t('heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderMetadataUrl-help')"
                :placeholder="$t('heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderMetadataUrl-placeholder')"
                v-model:value="item.config.identityProviderMetadataUrl"
            ></heptacom-admin-open-auth-url-field>
            <mt-textarea
                :disabled="!!item.config.identityProviderMetadataUrl"
                :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderMetadataXml')"
                :help-text="$t('heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderMetadataXml-help')"
                v-model="item.config.identityProviderMetadataXml"
            ></mt-textarea>
            <!-- manual config -->
            <mt-text-field
                :disabled="!!item.config.identityProviderMetadataUrl || !!item.config.identityProviderMetadataXml"
                :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderEntityId')"
                v-model="item.config.identityProviderEntityId"
            ></mt-text-field>
            <heptacom-admin-open-auth-url-field
                :omitUrlHash="true"
                :omitUrlSearch="true"
                :disabled="!!item.config.identityProviderMetadataUrl || !!item.config.identityProviderMetadataXml"
                :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderSsoUrl')"
                v-model:value="item.config.identityProviderSsoUrl"
            ></heptacom-admin-open-auth-url-field>
            <mt-textarea
                :disabled="!!item.config.identityProviderMetadataUrl || !!item.config.identityProviderMetadataXml"
                :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.identityProviderCertificate')"
                v-model="item.config.identityProviderCertificate"
            ></mt-textarea>
        </mt-card>
        {% endblock %}

        {% block heptacom_admin_open_auth_provider_saml2_settings_security %}
        <mt-card
            :title="$t('heptacomAdminOpenAuthClient.providerFields.saml2.security.cardTitle')"
            position-identifier="heptacom-admin-open-auth-provider-saml2-settings-security"
        >
            <sw-multi-select
                :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.security.requestedAuthnContexts')"
                :options="availableAuthnContexts"
                :help-text="$t('heptacomAdminOpenAuthClient.providerFields.saml2.security.requestedAuthnContextsHelp')"
                :placeholder="$t('heptacomAdminOpenAuthClient.providerFields.saml2.security.requestedAuthnContextsPlaceholder')"
                v-model:value="item.config.requestedAuthnContext"
            >
                </sw-multi-select>
        </mt-card>
        {% endblock %}

        {% block heptacom_admin_open_auth_provider_saml2_settings_attribute_mapping %}
        <mt-card
            :title="$t('heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.cardTitle')"
            position-identifier="heptacom-admin-open-auth-provider-saml2-settings-attribute-mapping"
        >
            {% block heptacom_admin_open_auth_provider_saml2_settings_attribute_mapping_inner %}
            <mt-select
                :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.templateSelect')"
                :placeholder="$t('heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.templateSelect-placeholder')"
                :options="mappingTemplateOptions"
                label-property="label"
                value-property="value"
                v-model="selectedMappingTemplate"
            ></mt-select>
            <div class="sw-field">
                <mt-button
                    :block="true"
                    size="small"
                    :disabled="!selectedMappingTemplate"
                    @click="onApplyMappingTemplate(selectedMappingTemplate)"
                    variant="secondary"
                >
                    {{ $t('heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.templateApply') }}
                </mt-button>
            </div>
            <template v-for="mappedProperty of availableProperties">
                <mt-text-field
                    :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.field.' + mappedProperty)"
                    v-model="item.config.attributeMapping[mappedProperty]"
                ></mt-text-field>
            </template>
            {% endblock %}
        </mt-card>
        {% endblock %}
    </div>
{% endblock %}
`});var dt={};a(dt,{default:()=>fi});var fi,ct=o(()=>{lt();fi={template:st,props:{item:{required:!0}},data(){return{selectedMappingTemplate:null,availableProperties:["firstName","lastName","email","timezone","locale","roles"],availableAuthnContexts:["urn:oasis:names:tc:SAML:2.0:ac:classes:unspecified","urn:oasis:names:tc:SAML:2.0:ac:classes:Kerberos","urn:oasis:names:tc:SAML:2.0:ac:classes:Password","urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport","urn:oasis:names:tc:SAML:2.0:ac:classes:Smartcard","urn:oasis:names:tc:SAML:2.0:ac:classes:SmartcardPKI","urn:oasis:names:tc:SAML:2.0:ac:classes:TLSClient","urn:oasis:names:tc:SAML:2.0:ac:classes:TimeSyncToken","urn:oasis:names:tc:SAML:2.0:ac:classes:X509","urn:federation:authentication:windows"].map(e=>({value:e,label:e.startsWith("urn:oasis:names:tc:SAML:2.0:ac:classes:")?e.substring(39):e})),attributeMappingTemplates:{friendlyNames:{firstName:"givenName",lastName:"surName",email:"emailAddress",roles:"memberOf"},x500:{firstName:"urn:oid:2.5.4.42",lastName:"urn:oid:2.5.4.4",email:"urn:oid:1.2.840.113549.1.9.1",roles:"urn:oid:1.3.6.1.4.1.5923.1.5.1.1"},entraId:{firstName:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname",lastName:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname",email:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress",roles:"http://schemas.microsoft.com/ws/2008/06/identity/claims/role"}}}},computed:{mappingTemplateOptions(){return Object.keys(this.attributeMappingTemplates).map(e=>({value:e,label:this.$t(`heptacomAdminOpenAuthClient.providerFields.saml2.attributeMapping.template.${e}`)}))}},watch:{item(e){e.config.attributeMapping||(e.config.attributeMapping={})}},methods:{onApplyMappingTemplate(e){let t=this.attributeMappingTemplates[e];this.item.config.attributeMapping=Object.assign(this.item.config.attributeMapping,t)}}}});var{Component:R}=Shopware;R.extend("heptacom-admin-open-auth-url-field","sw-url-field",()=>Promise.resolve().then(()=>(C(),k)));R.register("heptacom-admin-open-auth-user-confirm-login",()=>Promise.resolve().then(()=>(F(),P)));var q=`{% block sw_profile_index_general_password %}
    <template v-if="!denyPasswordLogin">
        {% parent %}
    </template>

    {% block sw_profile_index_admin_open_auth %}
        <mt-card
            position-identifier="heptacom-admin-open-auth-user-profile-sso"
            :title="$tc('sw-profile-index.titleHeptacomAdminOpenAuthCard')"
            :isLoading="isUserLoading || !languageId"
        >
            {% block sw_profile_index_admin_open_auth_clients %}
            <template #default>
                {% block sw_profile_index_admin_open_auth_clients_cards %}
                <sw-container rows="1fr">
                    <sw-card-section
                        v-for="client of heptacomAdminOpenAuthClients"
                        :key="client.id"
                        :slim="true"
                        divider="bottom"
                    >
                        {% block sw_profile_index_admin_open_auth_clients_cards_item %}
                        <sw-container columns="1fr auto">
                            {% block sw_profile_index_admin_open_auth_clients_cards_item_provider %}
                            <div>
                                {{ client.name }}
                            </div>
                            {% endblock %}

                            {% block sw_profile_index_admin_open_auth_clients_cards_item_action %}
                            <mt-button
                                v-if="client.connected"
                                @click="revokeHeptacomAdminOpenAuthUserKey(client.id)"
                                icon="regular-minus"
                            >
                                {{ $t('sw-profile-index.heptacomAdminOpenAuth.userKeys.actions.revoke') }}
                            </mt-button>
                            <mt-button
                                v-else
                                @click="redirectToLoginMask(client.id)"
                                icon="regular-plus"
                                variant="secondary"
                            >
                                {{ $t('sw-profile-index.heptacomAdminOpenAuth.userKeys.actions.connect') }}
                            </mt-button>
                            {% endblock %}
                        </sw-container>
                        {% endblock %}
                    </sw-card-section>
                </sw-container>
                {% endblock %}
            </template>
            {% endblock %}
        </mt-card>
    {% endblock %}
{% endblock %}
`;var{Component:At,Context:f}=Shopware;At.override("sw-profile-index-general",{template:q,inject:["repositoryFactory","systemConfigApiService"],data(){return{denyPasswordLogin:!1,heptacomAdminOpenAuthLoading:!0,heptacomAdminOpenAuthClients:[]}},computed:{heptacomAdminOpenAuthClientsRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client")},heptacomAdminOpenAuthHttpClient(){return this.heptacomAdminOpenAuthClientsRepository.httpClient}},watch:{isUserLoading:{handler(){this.loadHeptacomAdminOpenAuth().then()}},languages:{handler(){this.loadHeptacomAdminOpenAuth().then()}}},created(){this.systemConfigApiService.getValues("KskHeptacomAdminOpenAuth.config").then(e=>{this.denyPasswordLogin=e["KskHeptacomAdminOpenAuth.config.denyPasswordLogin"]}),this.loadHeptacomAdminOpenAuth()},methods:{async loadHeptacomAdminOpenAuth(){this.heptacomAdminOpenAuthLoading=!0,this.heptacomAdminOpenAuthClients=[];let e=this.heptacomAdminOpenAuthClientsRepository.buildHeaders(f.api),t=await this.heptacomAdminOpenAuthHttpClient.get("/_admin/open-auth/client/list",{headers:e});this.heptacomAdminOpenAuthClients=t.data.data,this.heptacomAdminOpenAuthLoading=!1},async redirectToLoginMask(e){let t=window.location.pathname+window.location.search+window.location.hash,i=this.heptacomAdminOpenAuthClientsRepository.buildHeaders(f.api),n=await this.heptacomAdminOpenAuthHttpClient.post(`/_action/open-auth/${e}/connect?redirectTo=${encodeURIComponent(t)}`,{},{headers:i});window.location.href=n.data.target},async revokeHeptacomAdminOpenAuthUserKey(e){let t=this.heptacomAdminOpenAuthClientsRepository.buildHeaders(f.api);await this.heptacomAdminOpenAuthHttpClient.post(`/_action/open-auth/${e}/disconnect`,{},{headers:t}),await this.loadHeptacomAdminOpenAuth()}}});var M=`{% block sw_settings_user_detail_content_confirm_password_modal_input__confirm_password %}
    <heptacom-admin-open-auth-user-confirm-login
        :divider="!denyPasswordLogin"
        ref="heptacomAdminOpenAuthUserConfirmLogin"
        @confirm="heptacomAdminOpenAuthUserConfirm"
    ></heptacom-admin-open-auth-user-confirm-login>

    <template v-if="!denyPasswordLogin">
        {% parent %}
    </template>
{% endblock %}

{% block sw_settings_user_detail_content_confirm_password_modal_actions_change %}
    <template v-if="!denyPasswordLogin">
        {% parent %}
    </template>
{% endblock %}
`;var{Component:yt}=Shopware;yt.override("sw-verify-user-modal",{template:M,inject:["systemConfigApiService"],data(){return{denyPasswordLogin:!1}},created(){this.systemConfigApiService.getValues("KskHeptacomAdminOpenAuth.config").then(e=>{this.denyPasswordLogin=e["KskHeptacomAdminOpenAuth.config.denyPasswordLogin"]})},methods:{onCloseConfirmPasswordModal(){return this.$refs.heptacomAdminOpenAuthUserConfirmLogin.abortAuthFlow(),this.$super("onCloseConfirmPasswordModal")},heptacomAdminOpenAuthUserConfirm(e){this.$emit("verified",e)}}});Shopware.Service("privileges").addPrivilegeMappingEntry({category:"permissions",parent:"settings",key:"heptacom_admin_open_auth_client",roles:{viewer:{privileges:["heptacom_admin_open_auth_client:read","heptacom_admin_open_auth_user_key:read","heptacom_admin_open_auth_client_rule:read","heptacom_admin_open_auth_client_rule_condition:read"],dependencies:[]},editor:{privileges:["heptacom_admin_open_auth_client:update","heptacom_admin_open_auth_client_rule:create","heptacom_admin_open_auth_client_rule:update","heptacom_admin_open_auth_client_rule:delete","heptacom_admin_open_auth_client_rule_condition:create","heptacom_admin_open_auth_client_rule_condition:update","heptacom_admin_open_auth_client_rule_condition:delete"],dependencies:["heptacom_admin_open_auth_client.viewer"]},creator:{privileges:["heptacom_admin_open_auth_client:create"],dependencies:["heptacom_admin_open_auth_client.editor"]},deleter:{privileges:["heptacom_admin_open_auth_client:delete"],dependencies:["heptacom_admin_open_auth_client.viewer"]}}});var{Component:c,Module:Et}=Shopware;c.register("heptacom-admin-open-auth-role-assignment-action-config",()=>Promise.resolve().then(()=>(D(),T)));c.register("heptacom-admin-open-auth-client-rule-container",()=>Promise.resolve().then(()=>(H(),I)));c.register("heptacom-admin-open-auth-client-rule-item",()=>Promise.resolve().then(()=>(X(),K)));c.register("heptacom-admin-open-auth-client-create-page",()=>Promise.resolve().then(()=>(Q(),Z)));c.register("heptacom-admin-open-auth-client-edit-page",()=>Promise.resolve().then(()=>(ne(),oe)));c.register("heptacom-admin-open-auth-client-listing-page",()=>Promise.resolve().then(()=>(de(),se)));Et.register("heptacom-admin-open-auth-client",{type:"plugin",name:"heptacom-admin-open-auth-client.module.name",title:"heptacom-admin-open-auth-client.module.title",description:"heptacom-admin-open-auth-client.module.description",color:"#FFC2A2",icon:"regular-sign-in",routes:{create:{component:"heptacom-admin-open-auth-client-create-page",path:"create",meta:{parentPath:"heptacom.admin.open.auth.client.settings",privilege:"heptacom_admin_open_auth_client.creator"}},edit:{component:"heptacom-admin-open-auth-client-edit-page",path:"edit/:id",meta:{parentPath:"heptacom.admin.open.auth.client.settings",privilege:"heptacom_admin_open_auth_client.editor"},props:{default(e){return{clientId:e.params.id}}}},settings:{component:"heptacom-admin-open-auth-client-listing-page",path:"settings",meta:{parentPath:"sw.settings.index.system",privilege:"heptacom_admin_open_auth_client.viewer"}}},settingsItem:[{to:"heptacom.admin.open.auth.client.settings",group:"system",icon:"regular-sign-in",privilege:"heptacom_admin_open_auth_client.viewer"}]});var ce=e=>(e.addCondition("heptacomAdminOpenAuthMicrosoftEntraIdOidcGroups",{component:"sw-condition-generic",label:"heptacomAdminOpenAuthClient.providerFields.microsoft_entra_id_oidc.condition.rule.groupIds",scopes:["microsoft_entra_id_oidc"],group:"user"}),e);var pe=e=>(e.addCondition("heptacomAdminOpenAuthAuthenticatedRequest",{component:"heptacom-admin-open-auth-condition-authenticated-request",label:"heptacomAdminOpenAuthClient.providerFields.open_id_connect.condition.rule.authenticatedRequest",scopes:["open_id_connect","cidaas","google_cloud","keycloak","microsoft_entra_id_oidc","okta","onelogin"],group:"user"}),e.addCondition("heptacomAdminOpenAuthAuthenticatedODataRequest",{component:"heptacom-admin-open-auth-condition-authenticated-request",label:"heptacomAdminOpenAuthClient.providerFields.open_id_connect.condition.rule.authenticatedODataRequest",scopes:["open_id_connect","cidaas","google_cloud","keycloak","microsoft_entra_id_oidc","okta","onelogin"],group:"user"}),e.addCondition("heptacomAdminOpenAuthIdToken",{component:"heptacom-admin-open-auth-condition-jmes-path",label:"heptacomAdminOpenAuthClient.providerFields.open_id_connect.condition.rule.idToken",scopes:["open_id_connect","cidaas","google_cloud","keycloak","microsoft_entra_id_oidc","okta","onelogin"],group:"user"}),e);var me=e=>(e.addCondition("heptacomAdminOpenAuthSaml2Role",{component:"sw-condition-generic",label:"heptacomAdminOpenAuthClient.providerFields.saml2.condition.rule.roles",scopes:["saml2"],group:"user"}),e);var he=`{% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_store_user_token %}
    <template v-if="!item || item.provider !== 'jumpcloud'">
        {% parent %}
    </template>
{% endblock %}
{% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner %}
    {% parent() %}

    <template v-if="item && item.provider === 'jumpcloud'">
        <mt-textarea
            :disabled="true"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate')"
            :help-text="$t('heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate-help')"
            v-model="item.config.serviceProviderCertificate"
        ></mt-textarea>
        <mt-textarea
            :disabled="true"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey')"
            :help-text="$t('heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey-help')"
            v-model="item.config.serviceProviderPublicKey"
        ></mt-textarea>
    </template>
{% endblock %}
`;var{Component:Ht}=Shopware;Ht.override("heptacom-admin-open-auth-client-edit-page",{template:he,data(){return{availableProperties:["firstName","lastName","email","timezone","locale"]}},watch:{item(e){e&&e.provider==="jumpcloud"&&(e.config.attributeMapping||(e.config.attributeMapping={}))}}});var ue=`{% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_store_user_token %}
    <template v-if="!item || item.provider !== 'saml2'">
        {% parent %}
    </template>
{% endblock %}
{% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner %}
    {% parent() %}

    <template v-if="item && item.provider === 'saml2'">
        <mt-textarea
            :disabled="true"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate')"
            :help-text="$t('heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate-help')"
            v-model="item.config.serviceProviderCertificate"
        ></mt-textarea>
        <mt-textarea
            :disabled="true"
            :label="$t('heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey')"
            :help-text="$t('heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey-help')"
            v-model="item.config.serviceProviderPublicKey"
        ></mt-textarea>
    </template>
{% endblock %}
`;var{Component:Bt}=Shopware;Bt.override("heptacom-admin-open-auth-client-edit-page",{template:ue,data(){return{selectedMappingTemplate:null,availableProperties:["firstName","lastName","email","timezone","locale"],attributeMappingTemplates:{friendlyNames:{firstName:"givenName",lastName:"surName",email:"emailAddress"},x500:{firstName:"urn:oid:2.5.4.42",lastName:"urn:oid:2.5.4.4",email:"urn:oid:1.2.840.113549.1.9.1"},entraId:{firstName:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname",lastName:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname",email:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"}}}},watch:{item(e){e&&e.provider==="saml2"&&(e.config.attributeMapping||(e.config.attributeMapping={}))}},methods:{onApplyMappingTemplate(e){let t=this.attributeMappingTemplates[e];this.item.config.attributeMapping=Object.assign(this.item.config.attributeMapping,t)}}});var{Application:b,Component:l}=Shopware;l.register("heptacom-admin-open-auth-provider-cidaas-settings",()=>Promise.resolve().then(()=>(ve(),fe)));l.register("heptacom-admin-open-auth-provider-google-cloud-settings",()=>Promise.resolve().then(()=>(ye(),we)));l.register("heptacom-admin-open-auth-provider-jira-settings",()=>Promise.resolve().then(()=>(Oe(),xe)));l.register("heptacom-admin-open-auth-provider-jumpcloud-settings",()=>Promise.resolve().then(()=>(Fe(),Pe)));l.register("heptacom-admin-open-auth-provider-keycloak-settings",()=>Promise.resolve().then(()=>(Ue(),Me)));l.register("heptacom-admin-open-auth-provider-microsoft-entra-id-oidc-settings",()=>Promise.resolve().then(()=>(je(),De)));b.addServiceProviderDecorator("heptacomOauthRuleDataProvider",ce);l.register("heptacom-admin-open-auth-provider-okta-settings",()=>Promise.resolve().then(()=>(Ne(),He)));l.register("heptacom-admin-open-auth-provider-onelogin-settings",()=>Promise.resolve().then(()=>(Xe(),Ke)));l.extend("heptacom-admin-open-auth-condition-authenticated-request","sw-condition-base",()=>Promise.resolve().then(()=>(Qe(),Ze)));l.extend("heptacom-admin-open-auth-condition-jmes-path","sw-condition-base",()=>Promise.resolve().then(()=>(it(),tt)));l.register("heptacom-admin-open-auth-provider-open-id-connect-settings",()=>Promise.resolve().then(()=>(rt(),at)));b.addServiceProviderDecorator("heptacomOauthRuleDataProvider",pe);l.register("heptacom-admin-open-auth-provider-saml2-settings",()=>Promise.resolve().then(()=>(ct(),dt)));b.addServiceProviderDecorator("heptacomOauthRuleDataProvider",me);var{Classes:vi}=Shopware,{ApiService:m}=vi,A=class extends m{constructor(t,i,n="heptacom_admin_open_auth_provider"){super(t,i,n)}list(){let t=this.getBasicHeaders();return this.httpClient.get(`_action/${this.getApiBasePath()}/list`,{headers:t}).then(i=>m.handleResponse(i))}factorize(t){let i=this.getBasicHeaders();return this.httpClient.post(`_action/${this.getApiBasePath()}/factorize`,{provider_key:t},{headers:i}).then(n=>m.handleResponse(n))}getRedirectUri(t){let i=this.getBasicHeaders();return this.httpClient.post(`_action/${this.getApiBasePath()}/client-redirect-url`,{client_id:t},{headers:i}).then(n=>m.handleResponse(n))}getMetadataUri(t){let i=this.getBasicHeaders();return this.httpClient.post(`_action/${this.getApiBasePath()}/client-metadata-url`,{client_id:t},{headers:i}).then(n=>m.handleResponse(n))}},pt=A;var{Classes:bi}=Shopware,{ApiService:mt}=bi,w=class extends mt{constructor(t,i,n="heptacom_admin_open_auth_rule_actions"){super(t,i,n)}list(){let t=this.getBasicHeaders();return this.httpClient.get(`_action/${this.getApiBasePath()}/list`,{headers:t}).then(i=>mt.handleResponse(i))}},ht=w;var u=class{$store={};operators={lowerThanEquals:{identifier:"<=",label:"global.sw-condition.operator.lowerThanEquals"},equals:{identifier:"=",label:"global.sw-condition.operator.equals"},greaterThanEquals:{identifier:">=",label:"global.sw-condition.operator.greaterThanEquals"},notEquals:{identifier:"!=",label:"global.sw-condition.operator.notEquals"},greaterThan:{identifier:">",label:"global.sw-condition.operator.greaterThan"},lowerThan:{identifier:"<",label:"global.sw-condition.operator.lowerThan"},isOneOf:{identifier:"=",label:"global.sw-condition.operator.isOneOf"},isNoneOf:{identifier:"!=",label:"global.sw-condition.operator.isNoneOf"},gross:{identifier:!1,label:"global.sw-condition.operator.gross"},net:{identifier:!0,label:"global.sw-condition.operator.net"},empty:{identifier:"empty",label:"global.sw-condition.operator.empty"}};operatorSets={defaultSet:[this.operators.equals,this.operators.notEquals,this.operators.greaterThanEquals,this.operators.lowerThanEquals],singleStore:[this.operators.equals,this.operators.notEquals],multiStore:[this.operators.isOneOf,this.operators.isNoneOf],string:[this.operators.equals,this.operators.notEquals],bool:[this.operators.equals],number:[this.operators.equals,this.operators.greaterThan,this.operators.greaterThanEquals,this.operators.lowerThan,this.operators.lowerThanEquals,this.operators.notEquals],date:[this.operators.equals,this.operators.greaterThan,this.operators.greaterThanEquals,this.operators.lowerThan,this.operators.lowerThanEquals,this.operators.notEquals],isNet:[this.operators.gross,this.operators.net],empty:[this.operators.empty],zipCode:[this.operators.greaterThan,this.operators.greaterThanEquals,this.operators.lowerThan,this.operators.lowerThanEquals]};groups={general:{id:"general",name:"sw-settings-rule.detail.groups.general"},user:{id:"user",name:"heptacom-admin-open-auth.condition.group.user"}};getByType(t){if(!t)return this.getByType("placeholder");if(t==="scriptRule"){let i=this.getConditions().filter(n=>n.type==="scriptRule").shift();if(i)return i}return this.$store[t]}getOperatorSet(t){return this.operatorSets[t]}addEmptyOperatorToOperatorSet(t){return t.concat(this.operatorSets.empty)}getOperatorSetByComponent(t){let i=t.config.componentName,n=t.type;return i==="sw-single-select"?this.operatorSets.singleStore:i==="sw-multi-select"?this.operatorSets.multiStore:n==="bool"?this.operatorSets.bool:n==="text"?this.operatorSets.string:n==="int"?this.operatorSets.number:this.operatorSets.defaultSet}getOperatorOptionsByIdentifiers(t,i=!1){return t.map(n=>{let r=Object.entries(this.operators).find(([s,d])=>i&&["equals","notEquals"].includes(s)||!i&&["isOneOf","isNoneOf"].includes(s)?!1:n===d.identifier);return r?r.pop():{identifier:n,label:`global.sw-condition.operator.${n}`}})}getByGroup(t){let i=Object.values(this.$store),n=[];return i.forEach(r=>{r.group===t&&n.push(r)}),n}getGroups(){return this.groups}upsertGroup(t,i){this.groups[t]={...this.groups[t],...i}}removeGroup(t){delete this.groups[t]}addCondition(t,i){i.type=t,this.$store[i.scriptId??t]=i}getConditions(t=null){let i=Object.values(this.$store);return t!==null&&(i=i.filter(n=>t.some(r=>n.scopes.indexOf(r)!==-1))),i}getComponentByCondition(t){if(this.isAndContainer(t))return"sw-condition-and-container";if(this.isOrContainer(t))return"sw-condition-or-container";if(this.isAllLineItemsContainer(t))return"sw-condition-all-line-items-container";if(!t.type)return"sw-condition-base";let i=this.getByType(t.type);return typeof i>"u"||!i.component?"sw-condition-not-found":i.component}getAndContainerData(){return{type:"andContainer",value:{}}}isAndContainer(t){return t.type==="andContainer"}getOrContainerData(){return{type:"orContainer",value:{}}}isOrContainer(t){return t.type==="orContainer"}getPlaceholderData(){return{type:null,value:{}}}isAllLineItemsContainer(t){return t.type==="allLineItemsContainer"}};var ut=e=>(e.addCondition("alwaysValid",{component:"sw-condition-is-always-valid",label:"global.sw-condition.condition.alwaysValidRule",scopes:["global"],group:"general"}),e.addCondition("dateRange",{component:"sw-condition-date-range",label:"global.sw-condition.condition.dateRangeRule.label",scopes:["global"],group:"general"}),e.addCondition("timeRange",{component:"sw-condition-time-range",label:"global.sw-condition.condition.timeRangeRule",scopes:["global"],group:"general"}),e.addCondition("heptacomAdminOpenAuthEmail",{component:"sw-condition-generic",label:"heptacom-admin-open-auth.condition.rule.email",scopes:["global"],group:"user"}),e.addCondition("heptacomAdminOpenAuthTimeZone",{component:"sw-condition-generic",label:"heptacom-admin-open-auth.condition.rule.timeZone",scopes:["jira","jumpcloud","keycloak","okta","open_id_connect","saml2"],group:"user"}),e.addCondition("heptacomAdminOpenAuthLocale",{component:"sw-condition-generic",label:"heptacom-admin-open-auth.condition.rule.locale",scopes:["google_cloud","jumpcloud","keycloak","okta","onelogin","open_id_connect","saml2"],group:"user"}),e.addCondition("heptacomAdminOpenAuthPrimaryKey",{component:"sw-condition-generic",label:"heptacom-admin-open-auth.condition.rule.primaryKey",scopes:["global"],group:"user"}),e);var{Application:y}=Shopware;y.addServiceProvider("HeptacomAdminOpenAuthProviderApiService",e=>{let t=y.getContainer("init");return new pt(t.httpClient,e.loginService)}).addServiceProvider("HeptacomAdminOpenAuthRuleActionsApiService",e=>{let t=y.getContainer("init");return new ht(t.httpClient,e.loginService)}).addServiceProvider("heptacomOauthRuleDataProvider",()=>new u).addServiceProviderDecorator("heptacomOauthRuleDataProvider",ut);})();
