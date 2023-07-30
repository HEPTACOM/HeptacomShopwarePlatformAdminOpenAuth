/*! For license information please see ksk-heptacom-admin-open-auth.js.LICENSE.txt */
!function(e){function t(t){for(var n,r,o=t[0],a=t[1],c=0,s=[];c<o.length;c++)r=o[c],Object.prototype.hasOwnProperty.call(i,r)&&i[r]&&s.push(i[r][0]),i[r]=0;for(n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n]);for(u&&u(t);s.length;)s.shift()()}var n={},r={"ksk-heptacom-admin-open-auth":0},i={"ksk-heptacom-admin-open-auth":0};function o(t){if(n[t])return n[t].exports;var r=n[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,o),r.l=!0,r.exports}o.e=function(e){var t=[];r[e]?t.push(r[e]):0!==r[e]&&{0:1,1:1,2:1,3:1,4:1}[e]&&t.push(r[e]=new Promise((function(t,n){for(var i="static/css/"+({}[e]||e)+".css",a=o.p+i,c=document.getElementsByTagName("link"),s=0;s<c.length;s++){var u=(l=c[s]).getAttribute("data-href")||l.getAttribute("href");if("stylesheet"===l.rel&&(u===i||u===a))return t()}var p=document.getElementsByTagName("style");for(s=0;s<p.length;s++){var l;if((u=(l=p[s]).getAttribute("data-href"))===i||u===a)return t()}var d=document.createElement("link");d.rel="stylesheet",d.type="text/css",d.onload=t,d.onerror=function(t){var i=t&&t.target&&t.target.src||a,o=new Error("Loading CSS chunk "+e+" failed.\n("+i+")");o.code="CSS_CHUNK_LOAD_FAILED",o.request=i,delete r[e],d.parentNode.removeChild(d),n(o)},d.href=a,document.getElementsByTagName("head")[0].appendChild(d)})).then((function(){r[e]=0})));var n=i[e];if(0!==n)if(n)t.push(n[2]);else{var a=new Promise((function(t,r){n=i[e]=[t,r]}));t.push(n[2]=a);var c,s=document.createElement("script");s.charset="utf-8",s.timeout=120,o.nc&&s.setAttribute("nonce",o.nc),s.src=function(e){return o.p+"static/js/"+({}[e]||e)+".js"}(e);var u=new Error;c=function(t){s.onerror=s.onload=null,clearTimeout(p);var n=i[e];if(0!==n){if(n){var r=t&&("load"===t.type?"missing":t.type),o=t&&t.target&&t.target.src;u.message="Loading chunk "+e+" failed.\n("+r+": "+o+")",u.name="ChunkLoadError",u.type=r,u.request=o,n[1](u)}i[e]=void 0}};var p=setTimeout((function(){c({type:"timeout",target:s})}),12e4);s.onerror=s.onload=c,document.head.appendChild(s)}return Promise.all(t)},o.m=e,o.c=n,o.d=function(e,t,n){o.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},o.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},o.t=function(e,t){if(1&t&&(e=o(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(o.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)o.d(n,r,function(t){return e[t]}.bind(null,r));return n},o.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return o.d(t,"a",t),t},o.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},o.p=(window.__sw__.assetPath + '/bundles/kskheptacomadminopenauth/'),o.oe=function(e){throw console.error(e),e};var a=this["webpackJsonpPluginksk-heptacom-admin-open-auth"]=this["webpackJsonpPluginksk-heptacom-admin-open-auth"]||[],c=a.push.bind(a);a.push=t,a=a.slice();for(var s=0;s<a.length;s++)t(a[s]);var u=c;o(o.s="0qf1")}({"0qf1":function(e,t,n){"use strict";n.r(t);n("uvv5");function r(e){return(r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function i(){i=function(){return e};var e={},t=Object.prototype,n=t.hasOwnProperty,o=Object.defineProperty||function(e,t,n){e[t]=n.value},a="function"==typeof Symbol?Symbol:{},c=a.iterator||"@@iterator",s=a.asyncIterator||"@@asyncIterator",u=a.toStringTag||"@@toStringTag";function p(e,t,n){return Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}),e[t]}try{p({},"")}catch(e){p=function(e,t,n){return e[t]=n}}function l(e,t,n,r){var i=t&&t.prototype instanceof m?t:m,a=Object.create(i.prototype),c=new C(r||[]);return o(a,"_invoke",{value:O(e,n,c)}),a}function d(e,t,n){try{return{type:"normal",arg:e.call(t,n)}}catch(e){return{type:"throw",arg:e}}}e.wrap=l;var h={};function m(){}function f(){}function v(){}var g={};p(g,c,(function(){return this}));var y=Object.getPrototypeOf,_=y&&y(y(L([])));_&&_!==t&&n.call(_,c)&&(g=_);var b=v.prototype=m.prototype=Object.create(g);function w(e){["next","throw","return"].forEach((function(t){p(e,t,(function(e){return this._invoke(t,e)}))}))}function A(e,t){function i(o,a,c,s){var u=d(e[o],e,a);if("throw"!==u.type){var p=u.arg,l=p.value;return l&&"object"==r(l)&&n.call(l,"__await")?t.resolve(l.__await).then((function(e){i("next",e,c,s)}),(function(e){i("throw",e,c,s)})):t.resolve(l).then((function(e){p.value=e,c(p)}),(function(e){return i("throw",e,c,s)}))}s(u.arg)}var a;o(this,"_invoke",{value:function(e,n){function r(){return new t((function(t,r){i(e,n,t,r)}))}return a=a?a.then(r,r):r()}})}function O(e,t,n){var r="suspendedStart";return function(i,o){if("executing"===r)throw new Error("Generator is already running");if("completed"===r){if("throw"===i)throw o;return S()}for(n.method=i,n.arg=o;;){var a=n.delegate;if(a){var c=k(a,n);if(c){if(c===h)continue;return c}}if("next"===n.method)n.sent=n._sent=n.arg;else if("throw"===n.method){if("suspendedStart"===r)throw r="completed",n.arg;n.dispatchException(n.arg)}else"return"===n.method&&n.abrupt("return",n.arg);r="executing";var s=d(e,t,n);if("normal"===s.type){if(r=n.done?"completed":"suspendedYield",s.arg===h)continue;return{value:s.arg,done:n.done}}"throw"===s.type&&(r="completed",n.method="throw",n.arg=s.arg)}}}function k(e,t){var n=t.method,r=e.iterator[n];if(void 0===r)return t.delegate=null,"throw"===n&&e.iterator.return&&(t.method="return",t.arg=void 0,k(e,t),"throw"===t.method)||"return"!==n&&(t.method="throw",t.arg=new TypeError("The iterator does not provide a '"+n+"' method")),h;var i=d(r,e.iterator,t.arg);if("throw"===i.type)return t.method="throw",t.arg=i.arg,t.delegate=null,h;var o=i.arg;return o?o.done?(t[e.resultName]=o.value,t.next=e.nextLoc,"return"!==t.method&&(t.method="next",t.arg=void 0),t.delegate=null,h):o:(t.method="throw",t.arg=new TypeError("iterator result is not an object"),t.delegate=null,h)}function P(e){var t={tryLoc:e[0]};1 in e&&(t.catchLoc=e[1]),2 in e&&(t.finallyLoc=e[2],t.afterLoc=e[3]),this.tryEntries.push(t)}function x(e){var t=e.completion||{};t.type="normal",delete t.arg,e.completion=t}function C(e){this.tryEntries=[{tryLoc:"root"}],e.forEach(P,this),this.reset(!0)}function L(e){if(e){var t=e[c];if(t)return t.call(e);if("function"==typeof e.next)return e;if(!isNaN(e.length)){var r=-1,i=function t(){for(;++r<e.length;)if(n.call(e,r))return t.value=e[r],t.done=!1,t;return t.value=void 0,t.done=!0,t};return i.next=i}}return{next:S}}function S(){return{value:void 0,done:!0}}return f.prototype=v,o(b,"constructor",{value:v,configurable:!0}),o(v,"constructor",{value:f,configurable:!0}),f.displayName=p(v,u,"GeneratorFunction"),e.isGeneratorFunction=function(e){var t="function"==typeof e&&e.constructor;return!!t&&(t===f||"GeneratorFunction"===(t.displayName||t.name))},e.mark=function(e){return Object.setPrototypeOf?Object.setPrototypeOf(e,v):(e.__proto__=v,p(e,u,"GeneratorFunction")),e.prototype=Object.create(b),e},e.awrap=function(e){return{__await:e}},w(A.prototype),p(A.prototype,s,(function(){return this})),e.AsyncIterator=A,e.async=function(t,n,r,i,o){void 0===o&&(o=Promise);var a=new A(l(t,n,r,i),o);return e.isGeneratorFunction(n)?a:a.next().then((function(e){return e.done?e.value:a.next()}))},w(b),p(b,u,"Generator"),p(b,c,(function(){return this})),p(b,"toString",(function(){return"[object Generator]"})),e.keys=function(e){var t=Object(e),n=[];for(var r in t)n.push(r);return n.reverse(),function e(){for(;n.length;){var r=n.pop();if(r in t)return e.value=r,e.done=!1,e}return e.done=!0,e}},e.values=L,C.prototype={constructor:C,reset:function(e){if(this.prev=0,this.next=0,this.sent=this._sent=void 0,this.done=!1,this.delegate=null,this.method="next",this.arg=void 0,this.tryEntries.forEach(x),!e)for(var t in this)"t"===t.charAt(0)&&n.call(this,t)&&!isNaN(+t.slice(1))&&(this[t]=void 0)},stop:function(){this.done=!0;var e=this.tryEntries[0].completion;if("throw"===e.type)throw e.arg;return this.rval},dispatchException:function(e){if(this.done)throw e;var t=this;function r(n,r){return a.type="throw",a.arg=e,t.next=n,r&&(t.method="next",t.arg=void 0),!!r}for(var i=this.tryEntries.length-1;i>=0;--i){var o=this.tryEntries[i],a=o.completion;if("root"===o.tryLoc)return r("end");if(o.tryLoc<=this.prev){var c=n.call(o,"catchLoc"),s=n.call(o,"finallyLoc");if(c&&s){if(this.prev<o.catchLoc)return r(o.catchLoc,!0);if(this.prev<o.finallyLoc)return r(o.finallyLoc)}else if(c){if(this.prev<o.catchLoc)return r(o.catchLoc,!0)}else{if(!s)throw new Error("try statement without catch or finally");if(this.prev<o.finallyLoc)return r(o.finallyLoc)}}}},abrupt:function(e,t){for(var r=this.tryEntries.length-1;r>=0;--r){var i=this.tryEntries[r];if(i.tryLoc<=this.prev&&n.call(i,"finallyLoc")&&this.prev<i.finallyLoc){var o=i;break}}o&&("break"===e||"continue"===e)&&o.tryLoc<=t&&t<=o.finallyLoc&&(o=null);var a=o?o.completion:{};return a.type=e,a.arg=t,o?(this.method="next",this.next=o.finallyLoc,h):this.complete(a)},complete:function(e,t){if("throw"===e.type)throw e.arg;return"break"===e.type||"continue"===e.type?this.next=e.arg:"return"===e.type?(this.rval=this.arg=e.arg,this.method="return",this.next="end"):"normal"===e.type&&t&&(this.next=t),h},finish:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var n=this.tryEntries[t];if(n.finallyLoc===e)return this.complete(n.completion,n.afterLoc),x(n),h}},catch:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var n=this.tryEntries[t];if(n.tryLoc===e){var r=n.completion;if("throw"===r.type){var i=r.arg;x(n)}return i}}throw new Error("illegal catch attempt")},delegateYield:function(e,t,n){return this.delegate={iterator:L(e),resultName:t,nextLoc:n},"next"===this.method&&(this.arg=void 0),h}},e}function o(e,t,n,r,i,o,a){try{var c=e[o](a),s=c.value}catch(e){return void n(e)}c.done?t(s):Promise.resolve(s).then(r,i)}function a(e){return function(){var t=this,n=arguments;return new Promise((function(r,i){var a=e.apply(t,n);function c(e){o(a,r,i,c,s,"next",e)}function s(e){o(a,r,i,c,s,"throw",e)}c(void 0)}))}}var c=Shopware,s=c.Component,u=c.Context;s.override("sw-profile-index-general",{template:'{% block sw_profile_index_general_password %}\n    <template v-if="!denyPasswordLogin">\n        {% parent %}\n    </template>\n\n    {% block sw_profile_index_admin_open_auth %}\n        <sw-card\n            position-identifier="heptacom-admin-open-auth-user-profile-sso"\n            :title="$tc(\'sw-profile-index.titleHeptacomAdminOpenAuthCard\')"\n            :isLoading="isUserLoading || !languageId"\n        >\n            {% block sw_profile_index_admin_open_auth_clients %}\n                <template>\n                    {% block sw_profile_index_admin_open_auth_clients_cards %}\n                        <sw-container rows="1fr">\n                            <sw-card-section\n                                v-for="client of heptacomAdminOpenAuthClients"\n                                :key="client.id"\n                                :slim="true"\n                                divider="bottom"\n                            >\n                                {% block sw_profile_index_admin_open_auth_clients_cards_item %}\n                                    <sw-container columns="1fr auto">\n                                        {% block sw_profile_index_admin_open_auth_clients_cards_item_provider %}\n                                            <div>\n                                                {{ client.name }}\n                                            </div>\n                                        {% endblock %}\n\n                                        {% block sw_profile_index_admin_open_auth_clients_cards_item_action %}\n                                            <sw-button\n                                                v-if="client.connected"\n                                                @click="revokeHeptacomAdminOpenAuthUserKey(client.id)"\n                                                icon="regular-minus"\n                                            >\n                                                {{ $t(\'sw-profile-index.heptacomAdminOpenAuth.userKeys.actions.revoke\') }}\n                                            </sw-button>\n                                            <sw-button\n                                                v-else-if="!client.connected"\n                                                @click="redirectToLoginMask(client.id)"\n                                                icon="regular-plus"\n                                            >\n                                                {{ $t(\'sw-profile-index.heptacomAdminOpenAuth.userKeys.actions.connect\') }}\n                                            </sw-button>\n                                        {% endblock %}\n                                    </sw-container>\n                                {% endblock %}\n                            </sw-card-section>\n                        </sw-container>\n                    {% endblock %}\n                </template>\n            {% endblock %}\n        </sw-card>\n    {% endblock %}\n{% endblock %}\n',inject:["repositoryFactory","systemConfigApiService"],data:function(){return{denyPasswordLogin:!1,heptacomAdminOpenAuthLoading:!0,heptacomAdminOpenAuthClients:[]}},computed:{heptacomAdminOpenAuthClientsRepository:function(){return this.repositoryFactory.create("heptacom_admin_open_auth_client")},heptacomAdminOpenAuthHttpClient:function(){return this.heptacomAdminOpenAuthClientsRepository.httpClient}},watch:{isUserLoading:{handler:function(){this.loadHeptacomAdminOpenAuth().then()}},languages:{handler:function(){this.loadHeptacomAdminOpenAuth().then()}}},created:function(){var e=this;this.systemConfigApiService.getValues("KskHeptacomAdminOpenAuth.config").then((function(t){e.denyPasswordLogin=t["KskHeptacomAdminOpenAuth.config.denyPasswordLogin"]}))},methods:{loadHeptacomAdminOpenAuth:function(){var e=this;return a(i().mark((function t(){var n,r;return i().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(!e.isUserLoading&&e.languages){t.next=2;break}return t.abrupt("return");case 2:return e.heptacomAdminOpenAuthLoading=!0,e.heptacomAdminOpenAuthClients=[],n=e.heptacomAdminOpenAuthClientsRepository.buildHeaders(u.api),t.next=7,e.heptacomAdminOpenAuthHttpClient.get("/_admin/open-auth/client/list",{headers:n});case 7:r=t.sent,e.heptacomAdminOpenAuthClients=r.data.data,e.heptacomAdminOpenAuthLoading=!1;case 10:case"end":return t.stop()}}),t)})))()},redirectToLoginMask:function(e){var t=this;return a(i().mark((function n(){var r,o,a;return i().wrap((function(n){for(;;)switch(n.prev=n.next){case 0:return r=window.location.pathname+window.location.search+window.location.hash,o=t.heptacomAdminOpenAuthClientsRepository.buildHeaders(u.api),n.next=4,t.heptacomAdminOpenAuthHttpClient.post("/_action/open-auth/".concat(e,"/connect?redirectTo=").concat(encodeURIComponent(r)),{},{headers:o});case 4:a=n.sent,window.location.href=a.data.target;case 6:case"end":return n.stop()}}),n)})))()},revokeHeptacomAdminOpenAuthUserKey:function(e){var t=this;return a(i().mark((function n(){var r;return i().wrap((function(n){for(;;)switch(n.prev=n.next){case 0:return r=t.heptacomAdminOpenAuthClientsRepository.buildHeaders(u.api),n.next=3,t.heptacomAdminOpenAuthHttpClient.post("/_action/open-auth/".concat(e,"/disconnect"),{},{headers:r});case 3:return n.next=5,t.loadHeptacomAdminOpenAuth();case 5:case"end":return n.stop()}}),n)})))()}}});Shopware.Component.override("sw-verify-user-modal",{template:'{% block sw_settings_user_detail_content_confirm_password_modal_input__confirm_password %}\n    <heptacom-admin-open-auth-user-confirm-login\n        :divider="!denyPasswordLogin"\n        ref="heptacomAdminOpenAuthUserConfirmLogin"\n        @confirm="heptacomAdminOpenAuthUserConfirm"\n    ></heptacom-admin-open-auth-user-confirm-login>\n\n    <template v-if="!denyPasswordLogin">\n        {% parent %}\n    </template>\n{% endblock %}\n\n{% block sw_settings_user_detail_content_confirm_password_modal_actions_change %}\n    <template v-if="!denyPasswordLogin">\n        {% parent %}\n    </template>\n{% endblock %}\n',inject:["systemConfigApiService"],data:function(){return{denyPasswordLogin:!1}},created:function(){var e=this;this.systemConfigApiService.getValues("KskHeptacomAdminOpenAuth.config").then((function(t){e.denyPasswordLogin=t["KskHeptacomAdminOpenAuth.config.denyPasswordLogin"]}))},methods:{onCloseConfirmPasswordModal:function(){return this.$refs.heptacomAdminOpenAuthUserConfirmLogin.abortAuthFlow(),this.$super("onCloseConfirmPasswordModal")},heptacomAdminOpenAuthUserConfirm:function(e){this.$emit("verified",e)}}});n("KnEA");var p=Shopware,l=p.Component,d=p.Module;l.register("heptacom-admin-open-auth-client-create-page",(function(){return n.e(2).then(n.bind(null,"LvZ1"))})),l.register("heptacom-admin-open-auth-client-edit-page",(function(){return n.e(3).then(n.bind(null,"JoBt"))})),l.register("heptacom-admin-open-auth-client-listing-page",(function(){return n.e(4).then(n.bind(null,"teqL"))})),d.register("heptacom-admin-open-auth-client",{type:"plugin",name:"heptacom-admin-open-auth-client.module.name",title:"heptacom-admin-open-auth-client.module.title",description:"heptacom-admin-open-auth-client.module.description",color:"#FFC2A2",icon:"regular-sign-in",routes:{create:{component:"heptacom-admin-open-auth-client-create-page",path:"create",meta:{parentPath:"heptacom.admin.open.auth.client.settings",privilege:"heptacom_admin_open_auth_client.creator"}},edit:{component:"heptacom-admin-open-auth-client-edit-page",path:"edit/:id",meta:{parentPath:"heptacom.admin.open.auth.client.settings",privilege:"heptacom_admin_open_auth_client.editor"},props:{default:function(e){return{clientId:e.params.id}}}},settings:{component:"heptacom-admin-open-auth-client-listing-page",path:"settings",meta:{parentPath:"sw.settings.index",privilege:"heptacom_admin_open_auth_client.viewer"}}},settingsItem:[{to:"heptacom.admin.open.auth.client.settings",group:"system",icon:"regular-sign-in",privilege:"heptacom_admin_open_auth_client.viewer"}]});Shopware.Component.override("heptacom-admin-open-auth-client-edit-page",{template:'{% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_store_user_token %}\n    <template v-if="!item || item.provider !== \'jumpcloud\'">\n        {% parent %}\n    </template>\n{% endblock %}\n\n{%  block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner %}\n    {% parent %}\n\n    <template v-if="item && item.provider === \'jumpcloud\'">\n        <sw-textarea-field\n            :disabled="true"\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate\')"\n            :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate-help\')"\n            v-model="item.config.serviceProviderCertificate"\n        ></sw-textarea-field>\n        <sw-textarea-field\n            :disabled="true"\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey\')"\n            :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey-help\')"\n            v-model="item.config.serviceProviderPublicKey"\n        ></sw-textarea-field>\n    </template>\n{% endblock %}\n\n',data:function(){return{availableProperties:["firstName","lastName","email","timezone","locale"]}},watch:{item:function(e){e&&"jumpcloud"===e.provider&&(e.config.attributeMapping||(e.config.attributeMapping={}))}}});Shopware.Component.override("heptacom-admin-open-auth-client-edit-page",{template:'{% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_store_user_token %}\n    <template v-if="!item || item.provider !== \'saml2\'">\n        {% parent %}\n    </template>\n{% endblock %}\n\n{%  block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner %}\n    {% parent %}\n\n    <template v-if="item && item.provider === \'saml2\'">\n        <sw-textarea-field\n            :disabled="true"\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate\')"\n            :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate-help\')"\n            v-model="item.config.serviceProviderCertificate"\n        ></sw-textarea-field>\n        <sw-textarea-field\n            :disabled="true"\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey\')"\n            :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey-help\')"\n            v-model="item.config.serviceProviderPublicKey"\n        ></sw-textarea-field>\n    </template>\n{% endblock %}\n',data:function(){return{selectedMappingTemplate:null,availableProperties:["firstName","lastName","email","timezone","locale"],attributeMappingTemplates:{friendlyNames:{firstName:"givenName",lastName:"surName",email:"emailAddress"},x500:{firstName:"urn:oid:2.5.4.42",lastName:"urn:oid:2.5.4.4",email:"urn:oid:1.2.840.113549.1.9.1"},azure:{firstName:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname",lastName:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname",email:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"}}}},watch:{item:function(e){e&&"saml2"===e.provider&&(e.config.attributeMapping||(e.config.attributeMapping={}))}},methods:{onApplyMappingTemplate:function(e){var t=this.attributeMappingTemplates[e];this.item.config.attributeMapping=Object.assign(this.item.config.attributeMapping,t)}}});var h=Shopware.Component;function m(e){return(m="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function f(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function v(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,(i=r.key,o=void 0,o=function(e,t){if("object"!==m(e)||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,t||"default");if("object"!==m(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(i,"string"),"symbol"===m(o)?o:String(o)),r)}var i,o}function g(e,t){return(g=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(e,t){return e.__proto__=t,e})(e,t)}function y(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}();return function(){var n,r=b(e);if(t){var i=b(this).constructor;n=Reflect.construct(r,arguments,i)}else n=r.apply(this,arguments);return _(this,n)}}function _(e,t){if(t&&("object"===m(t)||"function"==typeof t))return t;if(void 0!==t)throw new TypeError("Derived constructors may only return object or undefined");return function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e)}function b(e){return(b=Object.setPrototypeOf?Object.getPrototypeOf.bind():function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}h.register("heptacom-admin-open-auth-provider-cidaas-settings",(function(){return n.e(5).then(n.bind(null,"h0CI"))})),h.register("heptacom-admin-open-auth-provider-google-cloud-settings",(function(){return n.e(6).then(n.bind(null,"/7Bt"))})),h.register("heptacom-admin-open-auth-provider-jira-settings",(function(){return n.e(7).then(n.bind(null,"LQm9"))})),h.register("heptacom-admin-open-auth-provider-jumpcloud-settings",(function(){return n.e(8).then(n.bind(null,"I0LW"))})),h.register("heptacom-admin-open-auth-provider-keycloak-settings",(function(){return n.e(9).then(n.bind(null,"qk0B"))})),h.register("heptacom-admin-open-auth-provider-microsoft-azure-oidc-settings",(function(){return n.e(10).then(n.bind(null,"gsex"))})),h.register("heptacom-admin-open-auth-provider-okta-settings",(function(){return n.e(11).then(n.bind(null,"jYnA"))})),h.register("heptacom-admin-open-auth-provider-onelogin-settings",(function(){return n.e(12).then(n.bind(null,"CIRs"))})),h.register("heptacom-admin-open-auth-provider-open-id-connect-settings",(function(){return n.e(13).then(n.bind(null,"/QCv"))})),h.register("heptacom-admin-open-auth-provider-saml2-settings",(function(){return n.e(14).then(n.bind(null,"8FZX"))}));var w=Shopware.Classes.ApiService,A=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),Object.defineProperty(e,"prototype",{writable:!1}),t&&g(e,t)}(o,e);var t,n,r,i=y(o);function o(e,t){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"heptacom_admin_open_auth_provider";return f(this,o),i.call(this,e,t,n)}return t=o,(n=[{key:"list",value:function(){var e=this.getBasicHeaders();return this.httpClient.get("_action/".concat(this.getApiBasePath(),"/list"),{headers:e}).then((function(e){return w.handleResponse(e)}))}},{key:"factorize",value:function(e){var t=this.getBasicHeaders();return this.httpClient.post("_action/".concat(this.getApiBasePath(),"/factorize"),{provider_key:e},{headers:t}).then((function(e){return w.handleResponse(e)}))}},{key:"getRedirectUri",value:function(e){var t=this.getBasicHeaders();return this.httpClient.post("_action/".concat(this.getApiBasePath(),"/client-redirect-url"),{client_id:e},{headers:t}).then((function(e){return w.handleResponse(e)}))}},{key:"getMetadataUri",value:function(e){var t=this.getBasicHeaders();return this.httpClient.post("_action/".concat(this.getApiBasePath(),"/client-metadata-url"),{client_id:e},{headers:t}).then((function(e){return w.handleResponse(e)}))}}])&&v(t.prototype,n),r&&v(t,r),Object.defineProperty(t,"prototype",{writable:!1}),o}(w),O=Shopware.Application;O.addServiceProvider("HeptacomAdminOpenAuthProviderApiService",(function(e){var t=O.getContainer("init");return new A(t.httpClient,e.loginService)}))},KnEA:function(e,t){Shopware.Service("privileges").addPrivilegeMappingEntry({category:"permissions",parent:"settings",key:"heptacom_admin_open_auth_client",roles:{viewer:{privileges:["heptacom_admin_open_auth_client:read","heptacom_admin_open_auth_user_key:read"],dependencies:[]},editor:{privileges:["heptacom_admin_open_auth_client:update"],dependencies:["heptacom_admin_open_auth_client.viewer"]},creator:{privileges:["heptacom_admin_open_auth_client:create"],dependencies:["heptacom_admin_open_auth_client.editor"]},deleter:{privileges:["heptacom_admin_open_auth_client:delete"],dependencies:["heptacom_admin_open_auth_client.viewer"]}}})},uvv5:function(e,t,n){var r=Shopware.Component;r.register("heptacom-admin-open-auth-scope-field",(function(){return n.e(0).then(n.bind(null,"zHZc"))})),r.register("heptacom-admin-open-auth-user-confirm-login",(function(){return n.e(1).then(n.bind(null,"b/Rg"))}))}});
//# sourceMappingURL=ksk-heptacom-admin-open-auth.js.map