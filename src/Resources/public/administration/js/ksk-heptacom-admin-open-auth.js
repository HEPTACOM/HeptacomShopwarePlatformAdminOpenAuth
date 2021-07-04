(this.webpackJsonp=this.webpackJsonp||[]).push([["ksk-heptacom-admin-open-auth"],{"1Aqn":function(e,n,t){},"2uBD":function(e,n){e.exports='{% block sw_profile_index_content %}\n    {% parent %}\n\n    {% block sw_profile_index_heptacom_admin_open_auth_card %}\n        <sw-card\n            :title="$tc(\'sw-profile-index.titleHeptacomAdminOpenAuthCard\')"\n            :isLoading="isUserLoading"\n            class="sw-profile__card sw-card--grid"\n        >\n            {% block sw_profile_index_heptacom_admin_open_auth_card_clients %}\n                <template>\n                    {% block sw_profile_index_heptacom_admin_open_auth_card_clients_cards %}\n                        <sw-container rows="1fr">\n                            <sw-card-section\n                                v-for="client of heptacomAdminOpenAuthClients"\n                                :key="client.id"\n                                divider="bottom"\n                            >\n                                {% block sw_profile_index_heptacom_admin_open_auth_card_clients_cards_item %}\n                                    <sw-container columns="1fr auto">\n                                        {% block sw_profile_index_heptacom_admin_open_auth_card_clients_cards_item_provider %}\n                                            <div>\n                                                {{ client.name }}\n                                            </div>\n                                        {% endblock %}\n\n                                        {% block sw_profile_index_heptacom_admin_open_auth_card_clients_cards_item_action %}\n                                            <sw-button\n                                                v-if="client.userKeys.length > 0"\n                                                @click="revokeHeptacomAdminOpenAuthUserKey(client)"\n                                                icon="default-basic-x-line"\n                                            >\n                                                {{ $t(\'sw-profile-index.heptacomAdminOpenAuth.userKeys.actions.revoke\') }}\n                                            </sw-button>\n                                            <sw-button\n                                                v-else-if="client.active && client.connect"\n                                                @click="redirectToLoginMask(client.id)"\n                                                icon="default-basic-x-line"\n                                            >\n                                                {{ $t(\'sw-profile-index.heptacomAdminOpenAuth.userKeys.actions.connect\') }}\n                                            </sw-button>\n                                        {% endblock %}\n                                    </sw-container>\n                                {% endblock %}\n                            </sw-card-section>\n                        </sw-container>\n                    {% endblock %}\n                </template>\n            {% endblock %}\n        </sw-card>\n    {% endblock %}\n{% endblock %}\n'},"4CM4":function(e,n){e.exports='{% block heptacom_admin_open_auth_client_edit_page_content_provider_settings %}\n    {% parent %}\n\n    <sw-card\n        v-if="item && item.provider === \'jira\'"\n    >\n        <sw-text-field\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.jira.clientId\')"\n            v-model="item.config.clientId"\n        ></sw-text-field>\n        <sw-password-field\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.jira.clientSecret\')"\n            v-model="item.config.clientSecret"\n        ></sw-password-field>\n        <heptacom-admin-open-auth-scope-field\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.jira.additionalScopes\')"\n            :defaultScopes="[\'read:me\', \'read:jira-user\', \'offline_access\']"\n            v-model="item.config.scopes"\n        ></heptacom-admin-open-auth-scope-field>\n    </sw-card>\n{% endblock %}\n'},"7OyF":function(e,n){e.exports='{% block heptacom_admin_open_auth_client_listing_page %}\n    <sw-page class="heptacom-admin-open-auth-client-listing-page">\n        {% block heptacom_admin_open_auth_client_listing_page_inner %}\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_client_listing_page_search_bar %}\n            <template #search-bar>\n                <sw-search-bar\n                    :initialSearch="term"\n                    @search="onSearch"\n                    initialSearchType="heptacom_admin_open_auth_client"\n                ></sw-search-bar>\n            </template>\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_client_listing_page_search_bar_actions %}\n            <template #smart-bar-actions>\n                <sw-button\n                    :routerLink="{ name: \'heptacom.admin.open.auth.client.create\' }"\n                    variant="primary"\n                >\n                    {{ $t(\'heptacom-admin-open-auth-client.pages.listing.actions.create\') }}\n                </sw-button>\n            </template>\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_client_listing_page_content %}\n            <template #content>\n                {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing %}\n                    <sw-entity-listing\n                        v-if="items"\n                        :items="items"\n                        :repository="clientRepository"\n                        :showSelection="false"\n                        :columns="columns"\n                        :isLoading="!isLoading"\n                        :showActions="false"\n                    >\n                        {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing_inner %}\n                        {% endblock %}\n\n                        {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing_columns_active %}\n                            <template #column-active="{ item }">\n                                <sw-icon\n                                    :color="getLoginColor(item)"\n                                    name="default-action-log-in"\n                                    small\n                                ></sw-icon>\n                                <sw-icon\n                                    :color="getConnectColor(item)"\n                                    name="default-action-share"\n                                    small\n                                ></sw-icon>\n                            </template>\n                        {% endblock %}\n\n                        {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing_columns_created_at %}\n                            <template #column-createdAt="{ item }">\n                                {{ item.createdAt | date({ hour: \'2-digit\', minute: \'2-digit\' }) }}\n                            </template>\n                        {% endblock %}\n\n                        {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing_columns_provider %}\n                            <template #column-provider="{ item }">\n                                {{ $te(\'heptacomAdminOpenAuthClient.providers.\' + item.provider) ? $t(\'heptacomAdminOpenAuthClient.providers.\' + item.provider) : item.provider }}\n                            </template>\n                        {% endblock %}\n\n                        {% block heptacom_admin_open_auth_client_listing_page_content_entity_listing_pagination %}\n                            <template #pagination>\n                                <sw-pagination\n                                    :page="page"\n                                    :limit="limit"\n                                    :total="total"\n                                    :total-visible="7"\n                                    @page-change="onPageChange"\n                                ></sw-pagination>\n                            </template>\n                        {% endblock %}\n                    </sw-entity-listing>\n                {% endblock %}\n            </template>\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_client_listing_page_sidebar_container %}\n            <template #sidebar>\n                {% block heptacom_admin_open_auth_client_listing_page_sidebar %}\n                    <sw-sidebar>\n                        {% block heptacom_admin_open_auth_client_listing_page_sidebar_inner %}\n                        {% endblock %}\n\n                        {% block heptacom_admin_open_auth_client_listing_page_sidebar_refresh %}\n                            <sw-sidebar-item\n                                :title="$tc(\'heptacom-admin-open-auth-client.pages.listing.actions.refresh\')"\n                                @click="onRefresh"\n                                icon="default-arrow-360-left"\n                            ></sw-sidebar-item>\n                        {% endblock %}\n                    </sw-sidebar>\n                {% endblock %}\n            </template>\n        {% endblock %}\n    </sw-page>\n{% endblock %}\n'},DIaf:function(e){e.exports=JSON.parse('{"heptacomAdminOpenAuthClient":{"providers":{"microsoft_azure":"Microsoft Azure"},"providerFields":{"microsoft_azure":{"additionalScopes":"Application scopes","clientId":"Application identifier","clientSecret":"Application secret"}}}}')},Dg5b:function(e){e.exports=JSON.parse('{"sw-profile-index":{"titleHeptacomAdminOpenAuthCard":"OAuth","heptacomAdminOpenAuth":{"userKeys":{"columns":{"provider":"Provider","createdAt":"Connected at"},"actions":{"connect":"Connect","revoke":"Disconnect"}}}}}')},GKzT:function(e){e.exports=JSON.parse('{"heptacomAdminOpenAuthClient":{"providers":{"jira":"Atlassian Jira"},"providerFields":{"jira":{"additionalScopes":"Anwendungsberechtigungen","clientId":"Anwendungsschlüssel","clientSecret":"Anwendungsgehemnis"}}}}')},Gu25:function(e){e.exports=JSON.parse('{"heptacom-admin-open-auth-client":{"module":{"description":"Allow OAuth providers to provide admin logins","name":"HEPTACOM Open Authentication Login","title":"OAuth Login"},"pages":{"create":{"actions":{"create":"Choose"}},"edit":{"title":"Edit provider","actions":{"cancel":"Cancel","delete":"Delete","save":"Save"},"fields":{"name":"Name","active":"Active","login":"Use as login","connect":"Use to connect","storeUserToken":"Store user access keys","redirectUri":"Redirect URL"},"modals":{"delete":{"cancel":"Cancel","confirm":"Delete provider","explanation":"Careful, you are about to delete this provider permanently! If you proceed, logging in, connecting to or request data from this provider is not possible anymore.","title":"Delete provider","warning":"Are you sure you want to delete this provider?"}}},"listing":{"actions":{"create":"Create","refresh":"Refresh"},"columns":{"active":"State","createdAt":"Created","name":"Name","provider":"Provider","users":"Users"}}}}}')},"MKX+":function(e,n){e.exports='{% block heptacom_admin_open_auth_scope_field %}\n    <div class="heptacom-admin-open-auth-scope-field">\n        {% block heptacom_admin_open_auth_scope_field_confirm %}\n            <sw-confirm-field\n                v-bind="exceptInput($attrs)"\n                v-on="exceptInput($listeners)"\n                :preventEmptySubmit="true"\n                @input="addItem"\n                ref="confirmField"\n            ></sw-confirm-field>\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_scope_field_default_scopes_grid %}\n            <sw-grid\n                :header="false"\n                :selectable="false"\n                :table="true"\n                :items="defaultScopeItems">\n                {% block heptacom_admin_open_auth_scope_field_default_scopes_grid_inner %}\n                {% endblock %}\n\n                <template slot="columns" slot-scope="{ item }">\n                    {% block heptacom_admin_open_auth_scope_field_default_scopes_grid_columns %}\n                        {% block heptacom_admin_open_auth_scope_field_default_scopes_grid_columns_name %}\n                            <sw-grid-column>\n                                {{ item.name }}\n                            </sw-grid-column>\n                        {% endblock %}\n                    {% endblock %}\n                </template>\n            </sw-grid>\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_scope_field_items_grid %}\n            <sw-grid\n                :header="false"\n                :selectable="false"\n                :table="true"\n                :items="innerValue">\n                {% block heptacom_admin_open_auth_scope_field_items_grid_inner %}\n                {% endblock %}\n\n                <template slot="columns" slot-scope="{ item }">\n                    {% block heptacom_admin_open_auth_scope_field_items_grid_columns %}\n                        {% block heptacom_admin_open_auth_scope_field_items_grid_columns_name %}\n                            <sw-grid-column\n                                flex="1fr"\n                            >\n                                {{ item.name }}\n                            </sw-grid-column>\n                        {% endblock %}\n\n                        {% block heptacom_admin_open_auth_scope_field_items_grid_columns_actions %}\n                            <sw-grid-column\n                                align="right"\n                                flex="auto"\n                            >\n                                {% block heptacom_admin_open_auth_scope_field_items_grid_columns_actions_remove %}\n                                    <sw-button\n                                        @click="removeItem(item.name)"\n                                        size="x-small"\n                                        variant="danger"\n                                        square\n                                    >\n                                        <sw-icon\n                                            name="small-default-x-line-small"\n                                            small\n                                        ></sw-icon>\n                                    </sw-button>\n                                {% endblock %}\n                            </sw-grid-column>\n                        {% endblock %}\n                    {% endblock %}\n                </template>\n            </sw-grid>\n        {% endblock %}\n    </div>\n{% endblock %}\n'},OMuU:function(e,n,t){var i=t("XUUr");"string"==typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);(0,t("SZ7m").default)("03e4ebda",i,!0,{})},QtaA:function(e){e.exports=JSON.parse('{"heptacomAdminOpenAuthClient":{"providers":{"jira":"Atlassian Jira"},"providerFields":{"jira":{"additionalScopes":"Application scopes","clientId":"Application identifier","clientSecret":"Application secret"}}}}')},SfVv:function(e,n,t){"use strict";t.r(n);t("OMuU");var i=t("MKX+"),a=t.n(i);const{Component:o}=Shopware;o.register("heptacom-admin-open-auth-scope-field",{inheritAttrs:!1,template:a.a,props:{value:{required:!0,type:Array},defaultScopes:{required:!1,type:Array,default:()=>[]}},data(){return{innerValue:this.value.filter((e=>-1===this.defaultScopes.indexOf(e))).map((e=>({name:e})))}},watch:{value(e){this.items=e}},computed:{defaultScopeItems(){return this.defaultScopes.map((e=>({name:e})))},items:{get(){return this.innerValue.map((e=>e.name))},set(e){this.innerValue=e.map((e=>({name:e})))}}},methods:{addItem(e){this.isDefaultScope(e)||(this.innerValue=this.innerValue.filter((n=>n.name!==e)),this.innerValue.push({name:e}),this.$emit("input",this.items))},removeItem(e){this.innerValue=this.innerValue.filter((n=>n.name!==e)),this.$emit("input",this.items)},exceptInput:e=>e&&e.hasOwnProperty?Object.keys(e).reduce(((n,t)=>("input"!==t&&(n[t]=e[t]),n)),{}):e,isDefaultScope(e){return-1!==this.defaultScopes.findIndex((n=>n===e))}}});var c=t("2uBD"),s=t.n(c);const{Component:l,Context:r,Data:p}=Shopware,{Criteria:d}=p;l.override("sw-profile-index",{template:s.a,data:()=>({heptacomAdminOpenAuthLoading:!0,heptacomAdminOpenAuthClients:[]}),computed:{heptacomAdminOpenAuthClientsRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client")},heptacomAdminOpenAuthUserEmailsRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_user_email")},heptacomAdminOpenAuthUserKeysRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_user_key")},heptacomAdminOpenAuthUserTokensRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_user_token")}},methods:{loadHeptacomAdminOpenAuth(e){this.heptacomAdminOpenAuthLoading=!0,this.heptacomAdminOpenAuthClients=[];const n=new d;return n.getAssociation("userKeys").addFilter(d.equals("userId",e)),n.getAssociation("userEmails").addFilter(d.equals("userId",e)),n.getAssociation("userTokens").addFilter(d.equals("userId",e)),this.heptacomAdminOpenAuthClientsRepository.search(n,r.api).then((e=>{this.heptacomAdminOpenAuthClients=e.filter((e=>e.active&&e.connect||e.userKeys.length>0)),this.heptacomAdminOpenAuthLoading=!1}))},revokeHeptacomAdminOpenAuthUserKey(e){return Promise.all([...e.userKeys.map((e=>this.heptacomAdminOpenAuthUserKeysRepository.delete(e.id,r.api))),...e.userEmails.map((e=>this.heptacomAdminOpenAuthUserEmailsRepository.delete(e.id,r.api))),...e.userTokens.map((e=>this.heptacomAdminOpenAuthUserTokensRepository.delete(e.id,r.api)))]).then((()=>this.loadHeptacomAdminOpenAuth(this.user.id)))},redirectToLoginMask(e){const n=this.heptacomAdminOpenAuthClientsRepository.buildHeaders(r.api);this.heptacomAdminOpenAuthClientsRepository.httpClient.get(`/_admin/open-auth/${e}/connect`,{headers:n}).then((e=>{window.location.href=e.data.target}))},getUserData(){return this.$super("getUserData").then((e=>this.loadHeptacomAdminOpenAuth(e.id).then((()=>e))))}}});var m=t("Y3yI"),h=t.n(m);const{Component:_}=Shopware;_.register("heptacom-admin-open-auth-client-create-page",{template:h.a,inject:["HeptacomAdminOpenAuthProviderApiService"],data:()=>({isLoading:!0,items:null}),created(){this.loadData()},methods:{loadData(){this.isLoading=!0,this.loadProviders().then((()=>{this.isLoading=!1}))},loadProviders(){return this.items=[],this.HeptacomAdminOpenAuthProviderApiService.list().then((e=>{this.items=e.data.map((e=>({key:e,label:this.$t(`heptacomAdminOpenAuthClient.providers.${e}`),actionLabel:this.$te(`.heptacomAdminOpenAuthClient.providersCreate.${e}`)?this.$t(`heptacomAdminOpenAuthClient.providersCreate.${e}`):this.$t("heptacom-admin-open-auth-client.pages.create.actions.create"),classes:["heptacom-admin-open-auth-client-create-page-providers-provider",`heptacom-admin-open-auth-client-create-page-providers--provider-${e}`]}))).sort(((e,n)=>e.label.localeCompare(n.label))),this.isLoading=!1}))},createClient(e){return this.HeptacomAdminOpenAuthProviderApiService.factorize(e.key).then((e=>{this.$router.push({name:"heptacom.admin.open.auth.client.edit",params:{id:e.data.id}})}))}}});var u=t("v7gB"),g=t.n(u);const{Component:b,Context:f,Mixin:v}=Shopware;b.register("heptacom-admin-open-auth-client-edit-page",{template:g.a,inject:["repositoryFactory","HeptacomAdminOpenAuthProviderApiService"],mixins:[v.getByName("placeholder"),v.getByName("notification")],props:{clientId:{required:!0,type:String}},data:()=>({isLoading:!0,isSaveSuccessful:!1,item:null,showDeleteModal:!1,redirectUri:null}),created(){this.loadData()},computed:{dynamicName(){return this.placeholder(this.item,"name",this.$t("heptacom-admin-open-auth-client.pages.edit.title"))},clientRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client")}},methods:{loadData(){this.isLoading=!0,this.loadClient().finally((()=>{this.isLoading=!1}))},loadClient(){return this.item=null,this.clientRepository.get(this.clientId,f.api).then((e=>(this.item=e,this.HeptacomAdminOpenAuthProviderApiService.getRedirectUri(e.id)))).then((e=>{this.redirectUri=e.target}))},cancelEdit(){this.$router.push({name:this.$route.meta.parentPath})},saveItem(){this.isLoading=!0,this.clientRepository.save(this.item,f.api).then((()=>(this.isSaveSuccessful=!0,this.loadData()))).catch((e=>{const n=this.client.name;throw this.createNotificationError({title:this.$tc("global.notification.notificationSaveErrorTitle"),message:this.$tc("global.notification.notificationSaveErrorMessage",0,{entityName:n})}),e})).finally((()=>{this.isLoading=!1}))},onConfirmDelete(){return this.showDeleteModal=!1,this.isLoading=!0,this.clientRepository.delete(this.item.id,f.api).then((()=>{this.$router.push({name:"heptacom.admin.open.auth.client.settings"})})).catch((e=>{const n=this.client.name;throw this.createNotificationError({title:this.$tc("global.notification.notificationSaveErrorTitle"),message:this.$tc("global.notification.notificationSaveErrorMessage",0,{entityName:n})}),e})).finally((()=>{this.isLoading=!1}))}}});t("fGfE");var w=t("7OyF"),A=t.n(w);const{Component:k,Context:y,Data:S,Mixin:O}=Shopware,{Criteria:C}=S;k.register("heptacom-admin-open-auth-client-listing-page",{template:A.a,inject:["repositoryFactory"],mixins:[O.getByName("listing")],data(){return{isLoading:!0,items:null,columns:[{property:"active",label:this.$t("heptacom-admin-open-auth-client.pages.listing.columns.active"),allowResize:!1,width:"50px"},{property:"name",label:this.$t("heptacom-admin-open-auth-client.pages.listing.columns.name"),routerLink:"heptacom.admin.open.auth.client.edit"},{property:"provider",label:this.$t("heptacom-admin-open-auth-client.pages.listing.columns.provider")},{property:"userKeys.length",label:this.$t("heptacom-admin-open-auth-client.pages.listing.columns.users"),width:"100px"},{property:"createdAt",label:this.$t("heptacom-admin-open-auth-client.pages.listing.columns.createdAt"),width:"200px"}]}},created(){this.getList()},computed:{clientRepository(){return this.repositoryFactory.create("heptacom_admin_open_auth_client")},clientCriteria(){const e=new C,n=this.getMainListingParams();return e.addAssociation("userKeys"),e.setLimit(n.limit),e.setPage(n.page),e.addSorting(C.sort(n.sortBy||"name",n.sortDirection||"ASC")),n.term&&n.term.length&&e.addFilter(C.contains("name",n.term)),e}},methods:{getList(){return this.loadData()},loadData(){this.isLoading=!0,this.loadClients().then((()=>{this.isLoading=!1}))},loadClients(){return this.items=null,this.clientRepository.search(this.clientCriteria,y.api).then((e=>{this.items=e}))},getLoginColor:e=>e.active?e.login?"#00cc00":"#cc0000":"#333333",getConnectColor:e=>e.active?e.connect?"#00cc00":"#cc0000":"#333333"}});var x={"de-DE":t("sRdu"),"en-GB":t("Gu25")};const{Module:$}=Shopware;$.register("heptacom-admin-open-auth-client",{type:"plugin",name:"heptacom-admin-open-auth-client.module.name",title:"heptacom-admin-open-auth-client.module.title",description:"heptacom-admin-open-auth-client.module.description",color:"#FFC2A2",icon:"default-action-log-in",snippets:x,routes:{create:{component:"heptacom-admin-open-auth-client-create-page",path:"create",meta:{parentPath:"heptacom.admin.open.auth.client.settings"}},edit:{component:"heptacom-admin-open-auth-client-edit-page",path:"edit/:id",meta:{parentPath:"heptacom.admin.open.auth.client.settings"},props:{default:e=>({clientId:e.params.id})}},settings:{component:"heptacom-admin-open-auth-client-listing-page",path:"settings",meta:{parentPath:"sw.settings.index"}}},settingsItem:[{to:"heptacom.admin.open.auth.client.settings",group:"system",icon:"default-action-log-in"}]});var L=t("4CM4"),D=t.n(L);const{Component:U}=Shopware;U.override("heptacom-admin-open-auth-client-edit-page",{template:D.a});var I=t("rEcI"),R=t.n(I);const{Component:E}=Shopware;E.override("heptacom-admin-open-auth-client-edit-page",{template:R.a,watch:{item(e){e&&"microsoft_azure"===e.provider&&(e.config.scopes||(e.config.scopes=[]))}}});const{Classes:F}=Shopware,{ApiService:z}=F;var B=class extends z{constructor(e,n,t="heptacom_admin_open_auth_provider"){super(e,n,t)}list(){const e=this.getBasicHeaders();return this.httpClient.get(`_action/${this.getApiBasePath()}/list`,{headers:e}).then((e=>z.handleResponse(e)))}factorize(e){const n=this.getBasicHeaders();return this.httpClient.post(`_action/${this.getApiBasePath()}/factorize`,{provider_key:e},{headers:n}).then((e=>z.handleResponse(e)))}getRedirectUri(e){const n=this.getBasicHeaders();return this.httpClient.post(`_action/${this.getApiBasePath()}/client-redirect-url`,{client_id:e},{headers:n}).then((e=>z.handleResponse(e)))}};const{Application:N}=Shopware;N.addServiceProvider("HeptacomAdminOpenAuthProviderApiService",(e=>{const n=N.getContainer("init");return new B(n.httpClient,e.loginService)}));var P={"de-DE":t("ufXU"),"en-GB":t("nI2z")},M={"de-DE":t("kxYf"),"en-GB":t("Dg5b")},H={"de-DE":t("GKzT"),"en-GB":t("QtaA")},K={"de-DE":t("dDbS"),"en-GB":t("DIaf")};const{Locale:T}=Shopware;[P,M,H,K].map(Object.entries).flat().forEach((([e,n])=>T.extend(e,n)))},XUUr:function(e,n,t){},Y3yI:function(e,n){e.exports='{% block heptacom_admin_open_auth_client_create_page %}\n    <sw-page\n        :showSmartBar="false"\n        :showSearchBar="false"\n        class="heptacom-admin-open-auth-client-create-page"\n    >\n        {% block heptacom_admin_open_auth_client_create_page_inner %}{% endblock %}\n\n        {% block heptacom_admin_open_auth_client_create_page_content %}\n            <template #content>\n                <template\n                    v-if="isLoading"\n                >\n                    {% block heptacom_admin_open_auth_client_create_page_content_loader %}\n                        <sw-loader></sw-loader>\n                    {% endblock %}\n                </template>\n                <template\n                    v-else\n                >\n                    {% block heptacom_admin_open_auth_client_create_page_content_providers %}\n                        <sw-card-view>\n                            {% block heptacom_admin_open_auth_client_create_page_content_providers_list %}\n                                <sw-card\n                                    v-for="provider of items"\n                                    :key="provider.key"\n                                    :title="provider.label"\n                                    :classes="provider.classes"\n                                    hero\n                                    large\n                                >\n                                    {% block heptacom_admin_open_auth_client_create_page_content_providers_list_item %}\n                                        {% block heptacom_admin_open_auth_client_create_page_content_providers_list_item_action %}\n                                            <sw-button\n                                                @click="createClient(provider)"\n                                                class="heptacom-admin-open-auth-client-create-page-providers-provider--action"\n                                                variant="ghost"\n                                                block\n                                            >\n                                                {{ provider.actionLabel }}\n                                            </sw-button>\n                                        {% endblock %}\n                                    {% endblock %}\n                                </sw-card>\n                            {% endblock %}\n                        </sw-card-view>\n                    {% endblock %}\n                </template>\n            </template>\n        {% endblock %}\n    </sw-page>\n{% endblock %}\n'},dDbS:function(e){e.exports=JSON.parse('{"heptacomAdminOpenAuthClient":{"providers":{"microsoft_azure":"Microsoft Azure"},"providerFields":{"microsoft_azure":{"additionalScopes":"Anwendungsberechtigungen","clientId":"Anwendungsschlüssel","clientSecret":"Anwendungsgehemnis"}}}}')},fGfE:function(e,n,t){var i=t("1Aqn");"string"==typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);(0,t("SZ7m").default)("04d8061a",i,!0,{})},kxYf:function(e){e.exports=JSON.parse('{"sw-profile-index":{"titleHeptacomAdminOpenAuthCard":"OAuth","heptacomAdminOpenAuth":{"userKeys":{"columns":{"provider":"Anbieter","createdAt":"Verbunden am"},"actions":{"connect":"Verbinden","revoke":"Verbindung trennen"}}}}}')},nI2z:function(e){e.exports=JSON.parse('{"global":{"entities":{"heptacom_admin_open_auth_client":"OAuth Login"}}}')},rEcI:function(e,n){e.exports='{% block heptacom_admin_open_auth_client_edit_page_content_provider_settings %}\n    {% parent %}\n\n    <sw-card\n        v-if="item && item.provider === \'microsoft_azure\'"\n    >\n        <sw-text-field\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.microsoft_azure.clientId\')"\n            v-model="item.config.clientId"\n        ></sw-text-field>\n        <sw-password-field\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.microsoft_azure.clientSecret\')"\n            v-model="item.config.clientSecret"\n        ></sw-password-field>\n        <heptacom-admin-open-auth-scope-field\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.microsoft_azure.additionalScopes\')"\n            v-model="item.config.scopes"\n        ></heptacom-admin-open-auth-scope-field>\n    </sw-card>\n{% endblock %}\n'},sRdu:function(e){e.exports=JSON.parse('{"heptacom-admin-open-auth-client":{"module":{"description":"Erlaubt die Anmeldung über OAuth Anbieter","name":"HEPTACOM Open Authentication Login","title":"OAuth Anmeldung"},"pages":{"create":{"actions":{"create":"Auswählen"}},"edit":{"title":"Anbieter bearbeiter","actions":{"cancel":"Abbrechen","delete":"Löschen","save":"Speichern"},"fields":{"name":"Name","active":"Aktiv","login":"Als Anmeldung verwenden","connect":"Zum Verknüpfen verwenden","storeUserToken":"Benutzerberechtigungsschlüssel speichern","redirectUri":"Rücksende-URL"},"modals":{"delete":{"cancel":"Abbrechen","confirm":"Anbieter löschen","explanation":"Dieser Vorgang ist unwiderruflich. Bei Löschung funktionieren keine Anmeldungen, Verknüpfungen oder Anfragen mehr zu diesem Anbieter","title":"Anbieter löschen","warning":"Sind Sie sich sicher, dass Sie diese Anbieterkonfiguration löschen wollen?"}}},"listing":{"actions":{"create":"Erstellen","refresh":"Neuladen"},"columns":{"active":"Status","createdAt":"Erstellt","name":"Name","provider":"Anbieter","users":"Benutzer"}}}}}')},ufXU:function(e){e.exports=JSON.parse('{"global":{"entities":{"heptacom_admin_open_auth_client":"OAuth Anmeldung"}}}')},v7gB:function(e,n){e.exports='{% block heptacom_admin_open_auth_client_edit_page %}\n    <sw-page\n        :showSearchBar="false"\n        class="heptacom-admin-open-auth-client-edit-page"\n    >\n        {% block heptacom_admin_open_auth_client_edit_page_inner %}\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_client_edit_page_smart_bar_header %}\n            <template #smart-bar-header>\n                {{ dynamicName }}\n            </template>\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_client_edit_page_search_bar_actions %}\n            <template #smart-bar-actions>\n                {% block heptacom_admin_open_auth_client_edit_page_search_bar_actions_delete %}\n                    <sw-button\n                        :disabled="isLoading"\n                        @click="showDeleteModal = true"\n                        variant="danger"\n                    >\n                        {{ $t(\'heptacom-admin-open-auth-client.pages.edit.actions.delete\') }}\n                    </sw-button>\n                {% endblock %}\n\n                {% block heptacom_admin_open_auth_client_edit_page_search_bar_actions_cancel %}\n                    <sw-button\n                        :disabled="isLoading"\n                        @click="cancelEdit"\n                    >\n                        {{ $t(\'heptacom-admin-open-auth-client.pages.edit.actions.cancel\') }}\n                    </sw-button>\n                {% endblock %}\n\n                {% block heptacom_admin_open_auth_client_edit_page_search_bar_actions_save %}\n                    <sw-button-process\n                        :disabled="isLoading"\n                        :isLoading="isLoading"\n                        @click.prevent="saveItem"\n                        v-model="isSaveSuccessful"\n                        variant="primary"\n                    >\n                        {{ $t(\'heptacom-admin-open-auth-client.pages.edit.actions.save\') }}\n                    </sw-button-process>\n                {% endblock %}\n            </template>\n        {% endblock %}\n\n        {% block heptacom_admin_open_auth_client_edit_page_content %}\n            <template #content>\n                <sw-card-view>\n                    {% block heptacom_admin_open_auth_client_edit_page_content_base_settings %}\n                        <sw-card\n                            :isLoading="isLoading"\n                        >\n                            <template v-if="item">\n                                <sw-text-field\n                                    :label="$t(\'heptacom-admin-open-auth-client.pages.edit.fields.name\')"\n                                    v-model="item.name"\n                                ></sw-text-field>\n                                <sw-switch-field\n                                    :label="$t(\'heptacom-admin-open-auth-client.pages.edit.fields.active\')"\n                                    v-model="item.active"\n                                ></sw-switch-field>\n                                <sw-switch-field\n                                    :disabled="!item.active"\n                                    :label="$t(\'heptacom-admin-open-auth-client.pages.edit.fields.login\')"\n                                    v-model="item.login"\n                                ></sw-switch-field>\n                                <sw-switch-field\n                                    :disabled="!item.active"\n                                    :label="$t(\'heptacom-admin-open-auth-client.pages.edit.fields.connect\')"\n                                    v-model="item.connect"\n                                ></sw-switch-field>\n                                <sw-switch-field\n                                    :disabled="!item.active"\n                                    :label="$t(\'heptacom-admin-open-auth-client.pages.edit.fields.storeUserToken\')"\n                                    v-model="item.storeUserToken"\n                                ></sw-switch-field>\n                                <sw-text-field\n                                    v-if="redirectUri"\n                                    :copyable="true"\n                                    :copyableTooltip="true"\n                                    :label="$t(\'heptacom-admin-open-auth-client.pages.edit.fields.redirectUri\')"\n                                    :value="redirectUri"\n                                    disabled\n                                ></sw-text-field>\n                            </template>\n                        </sw-card>\n                    {% endblock %}\n\n                    {% block heptacom_admin_open_auth_client_edit_page_content_provider_settings %}{% endblock %}\n                </sw-card-view>\n\n                {% block heptacom_admin_open_auth_client_edit_page_content_modal_delete %}\n                    <sw-modal\n                        v-if="showDeleteModal"\n                        :title="$t(\'heptacom-admin-open-auth-client.pages.edit.modals.delete.title\')"\n                        @modal-close="showDeleteModal = false"\n                        variant="small"\n                    >\n                        {% block heptacom_admin_open_auth_client_edit_page_content_modal_delete_confirm_text %}\n                            <p>\n                                {{ $t(\'heptacom-admin-open-auth-client.pages.edit.modals.delete.warning\') }}\n                            </p>\n                            <p>\n                                <strong>{{ dynamicName }}</strong>\n                            </p>\n                            <p>\n                                {{ $t(\'heptacom-admin-open-auth-client.pages.edit.modals.delete.explanation\') }}\n                            </p>\n                        {% endblock %}\n\n                        {% block heptacom_admin_open_auth_client_edit_page_content_modal_delete_footer %}\n                            <template #modal-footer>\n                                {% block heptacom_admin_open_auth_client_edit_page_content_modal_delete_abort %}\n                                    <sw-button\n                                        size="small"\n                                        @click="showDeleteModal = false"\n                                    >\n                                        {{ $t(\'heptacom-admin-open-auth-client.pages.edit.modals.delete.cancel\') }}\n                                    </sw-button>\n                                {% endblock %}\n\n                                {% block heptacom_admin_open_auth_client_edit_page_content_modal_delete_delete %}\n                                    <sw-button\n                                        size="small"\n                                        variant="danger"\n                                        @click="onConfirmDelete"\n                                    >\n                                        {{ $t(\'heptacom-admin-open-auth-client.pages.edit.modals.delete.confirm\') }}\n                                    </sw-button>\n                                {% endblock %}\n                            </template>\n                        {% endblock %}\n                    </sw-modal>\n                {% endblock %}\n            </template>\n        {% endblock %}\n    </sw-page>\n{% endblock %}\n'}},[["SfVv","runtime","vendors-node"]]]);