/*! For license information please see ksk-heptacom-admin-open-auth.js.LICENSE.txt */
!function(e){function t(t){for(var n,r,i=t[0],a=t[1],c=0,s=[];c<i.length;c++)r=i[c],Object.prototype.hasOwnProperty.call(o,r)&&o[r]&&s.push(o[r][0]),o[r]=0;for(n in a)Object.prototype.hasOwnProperty.call(a,n)&&(e[n]=a[n]);for(u&&u(t);s.length;)s.shift()()}var n={},r={"ksk-heptacom-admin-open-auth":0},o={"ksk-heptacom-admin-open-auth":0};function i(t){if(n[t])return n[t].exports;var r=n[t]={i:t,l:!1,exports:{}};return e[t].call(r.exports,r,r.exports,i),r.l=!0,r.exports}i.e=function(e){var t=[];r[e]?t.push(r[e]):0!==r[e]&&{1:1,2:1,3:1,4:1,5:1,6:1}[e]&&t.push(r[e]=new Promise((function(t,n){for(var o="static/css/"+({}[e]||e)+".css",a=i.p+o,c=document.getElementsByTagName("link"),s=0;s<c.length;s++){var u=(p=c[s]).getAttribute("data-href")||p.getAttribute("href");if("stylesheet"===p.rel&&(u===o||u===a))return t()}var l=document.getElementsByTagName("style");for(s=0;s<l.length;s++){var p;if((u=(p=l[s]).getAttribute("data-href"))===o||u===a)return t()}var d=document.createElement("link");d.rel="stylesheet",d.type="text/css",d.onload=t,d.onerror=function(t){var o=t&&t.target&&t.target.src||a,i=new Error("Loading CSS chunk "+e+" failed.\n("+o+")");i.code="CSS_CHUNK_LOAD_FAILED",i.request=o,delete r[e],d.parentNode.removeChild(d),n(i)},d.href=a,document.getElementsByTagName("head")[0].appendChild(d)})).then((function(){r[e]=0})));var n=o[e];if(0!==n)if(n)t.push(n[2]);else{var a=new Promise((function(t,r){n=o[e]=[t,r]}));t.push(n[2]=a);var c,s=document.createElement("script");s.charset="utf-8",s.timeout=120,i.nc&&s.setAttribute("nonce",i.nc),s.src=function(e){return i.p+"static/js/"+({}[e]||e)+".js"}(e);var u=new Error;c=function(t){s.onerror=s.onload=null,clearTimeout(l);var n=o[e];if(0!==n){if(n){var r=t&&("load"===t.type?"missing":t.type),i=t&&t.target&&t.target.src;u.message="Loading chunk "+e+" failed.\n("+r+": "+i+")",u.name="ChunkLoadError",u.type=r,u.request=i,n[1](u)}o[e]=void 0}};var l=setTimeout((function(){c({type:"timeout",target:s})}),12e4);s.onerror=s.onload=c,document.head.appendChild(s)}return Promise.all(t)},i.m=e,i.c=n,i.d=function(e,t,n){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:n})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(e,t){if(1&t&&(e=i(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(i.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var r in e)i.d(n,r,function(t){return e[t]}.bind(null,r));return n},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p=(window.__sw__.assetPath + '/bundles/kskheptacomadminopenauth/'),i.oe=function(e){throw console.error(e),e};var a=this["webpackJsonpPluginksk-heptacom-admin-open-auth"]=this["webpackJsonpPluginksk-heptacom-admin-open-auth"]||[],c=a.push.bind(a);a.push=t,a=a.slice();for(var s=0;s<a.length;s++)t(a[s]);var u=c;i(i.s="0qf1")}({"0qf1":function(e,t,n){"use strict";n.r(t);n("uvv5");function r(e){return(r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function o(){o=function(){return e};var e={},t=Object.prototype,n=t.hasOwnProperty,i=Object.defineProperty||function(e,t,n){e[t]=n.value},a="function"==typeof Symbol?Symbol:{},c=a.iterator||"@@iterator",s=a.asyncIterator||"@@asyncIterator",u=a.toStringTag||"@@toStringTag";function l(e,t,n){return Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}),e[t]}try{l({},"")}catch(e){l=function(e,t,n){return e[t]=n}}function p(e,t,n,r){var o=t&&t.prototype instanceof m?t:m,a=Object.create(o.prototype),c=new S(r||[]);return i(a,"_invoke",{value:A(e,n,c)}),a}function d(e,t,n){try{return{type:"normal",arg:e.call(t,n)}}catch(e){return{type:"throw",arg:e}}}e.wrap=p;var h={};function m(){}function f(){}function g(){}var v={};l(v,c,(function(){return this}));var y=Object.getPrototypeOf,b=y&&y(y(x([])));b&&b!==t&&n.call(b,c)&&(v=b);var _=g.prototype=m.prototype=Object.create(v);function w(e){["next","throw","return"].forEach((function(t){l(e,t,(function(e){return this._invoke(t,e)}))}))}function O(e,t){function o(i,a,c,s){var u=d(e[i],e,a);if("throw"!==u.type){var l=u.arg,p=l.value;return p&&"object"==r(p)&&n.call(p,"__await")?t.resolve(p.__await).then((function(e){o("next",e,c,s)}),(function(e){o("throw",e,c,s)})):t.resolve(p).then((function(e){l.value=e,c(l)}),(function(e){return o("throw",e,c,s)}))}s(u.arg)}var a;i(this,"_invoke",{value:function(e,n){function r(){return new t((function(t,r){o(e,n,t,r)}))}return a=a?a.then(r,r):r()}})}function A(e,t,n){var r="suspendedStart";return function(o,i){if("executing"===r)throw new Error("Generator is already running");if("completed"===r){if("throw"===o)throw i;return j()}for(n.method=o,n.arg=i;;){var a=n.delegate;if(a){var c=k(a,n);if(c){if(c===h)continue;return c}}if("next"===n.method)n.sent=n._sent=n.arg;else if("throw"===n.method){if("suspendedStart"===r)throw r="completed",n.arg;n.dispatchException(n.arg)}else"return"===n.method&&n.abrupt("return",n.arg);r="executing";var s=d(e,t,n);if("normal"===s.type){if(r=n.done?"completed":"suspendedYield",s.arg===h)continue;return{value:s.arg,done:n.done}}"throw"===s.type&&(r="completed",n.method="throw",n.arg=s.arg)}}}function k(e,t){var n=t.method,r=e.iterator[n];if(void 0===r)return t.delegate=null,"throw"===n&&e.iterator.return&&(t.method="return",t.arg=void 0,k(e,t),"throw"===t.method)||"return"!==n&&(t.method="throw",t.arg=new TypeError("The iterator does not provide a '"+n+"' method")),h;var o=d(r,e.iterator,t.arg);if("throw"===o.type)return t.method="throw",t.arg=o.arg,t.delegate=null,h;var i=o.arg;return i?i.done?(t[e.resultName]=i.value,t.next=e.nextLoc,"return"!==t.method&&(t.method="next",t.arg=void 0),t.delegate=null,h):i:(t.method="throw",t.arg=new TypeError("iterator result is not an object"),t.delegate=null,h)}function C(e){var t={tryLoc:e[0]};1 in e&&(t.catchLoc=e[1]),2 in e&&(t.finallyLoc=e[2],t.afterLoc=e[3]),this.tryEntries.push(t)}function P(e){var t=e.completion||{};t.type="normal",delete t.arg,e.completion=t}function S(e){this.tryEntries=[{tryLoc:"root"}],e.forEach(C,this),this.reset(!0)}function x(e){if(e){var t=e[c];if(t)return t.call(e);if("function"==typeof e.next)return e;if(!isNaN(e.length)){var r=-1,o=function t(){for(;++r<e.length;)if(n.call(e,r))return t.value=e[r],t.done=!1,t;return t.value=void 0,t.done=!0,t};return o.next=o}}return{next:j}}function j(){return{value:void 0,done:!0}}return f.prototype=g,i(_,"constructor",{value:g,configurable:!0}),i(g,"constructor",{value:f,configurable:!0}),f.displayName=l(g,u,"GeneratorFunction"),e.isGeneratorFunction=function(e){var t="function"==typeof e&&e.constructor;return!!t&&(t===f||"GeneratorFunction"===(t.displayName||t.name))},e.mark=function(e){return Object.setPrototypeOf?Object.setPrototypeOf(e,g):(e.__proto__=g,l(e,u,"GeneratorFunction")),e.prototype=Object.create(_),e},e.awrap=function(e){return{__await:e}},w(O.prototype),l(O.prototype,s,(function(){return this})),e.AsyncIterator=O,e.async=function(t,n,r,o,i){void 0===i&&(i=Promise);var a=new O(p(t,n,r,o),i);return e.isGeneratorFunction(n)?a:a.next().then((function(e){return e.done?e.value:a.next()}))},w(_),l(_,u,"Generator"),l(_,c,(function(){return this})),l(_,"toString",(function(){return"[object Generator]"})),e.keys=function(e){var t=Object(e),n=[];for(var r in t)n.push(r);return n.reverse(),function e(){for(;n.length;){var r=n.pop();if(r in t)return e.value=r,e.done=!1,e}return e.done=!0,e}},e.values=x,S.prototype={constructor:S,reset:function(e){if(this.prev=0,this.next=0,this.sent=this._sent=void 0,this.done=!1,this.delegate=null,this.method="next",this.arg=void 0,this.tryEntries.forEach(P),!e)for(var t in this)"t"===t.charAt(0)&&n.call(this,t)&&!isNaN(+t.slice(1))&&(this[t]=void 0)},stop:function(){this.done=!0;var e=this.tryEntries[0].completion;if("throw"===e.type)throw e.arg;return this.rval},dispatchException:function(e){if(this.done)throw e;var t=this;function r(n,r){return a.type="throw",a.arg=e,t.next=n,r&&(t.method="next",t.arg=void 0),!!r}for(var o=this.tryEntries.length-1;o>=0;--o){var i=this.tryEntries[o],a=i.completion;if("root"===i.tryLoc)return r("end");if(i.tryLoc<=this.prev){var c=n.call(i,"catchLoc"),s=n.call(i,"finallyLoc");if(c&&s){if(this.prev<i.catchLoc)return r(i.catchLoc,!0);if(this.prev<i.finallyLoc)return r(i.finallyLoc)}else if(c){if(this.prev<i.catchLoc)return r(i.catchLoc,!0)}else{if(!s)throw new Error("try statement without catch or finally");if(this.prev<i.finallyLoc)return r(i.finallyLoc)}}}},abrupt:function(e,t){for(var r=this.tryEntries.length-1;r>=0;--r){var o=this.tryEntries[r];if(o.tryLoc<=this.prev&&n.call(o,"finallyLoc")&&this.prev<o.finallyLoc){var i=o;break}}i&&("break"===e||"continue"===e)&&i.tryLoc<=t&&t<=i.finallyLoc&&(i=null);var a=i?i.completion:{};return a.type=e,a.arg=t,i?(this.method="next",this.next=i.finallyLoc,h):this.complete(a)},complete:function(e,t){if("throw"===e.type)throw e.arg;return"break"===e.type||"continue"===e.type?this.next=e.arg:"return"===e.type?(this.rval=this.arg=e.arg,this.method="return",this.next="end"):"normal"===e.type&&t&&(this.next=t),h},finish:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var n=this.tryEntries[t];if(n.finallyLoc===e)return this.complete(n.completion,n.afterLoc),P(n),h}},catch:function(e){for(var t=this.tryEntries.length-1;t>=0;--t){var n=this.tryEntries[t];if(n.tryLoc===e){var r=n.completion;if("throw"===r.type){var o=r.arg;P(n)}return o}}throw new Error("illegal catch attempt")},delegateYield:function(e,t,n){return this.delegate={iterator:x(e),resultName:t,nextLoc:n},"next"===this.method&&(this.arg=void 0),h}},e}function i(e,t,n,r,o,i,a){try{var c=e[i](a),s=c.value}catch(e){return void n(e)}c.done?t(s):Promise.resolve(s).then(r,o)}function a(e){return function(){var t=this,n=arguments;return new Promise((function(r,o){var a=e.apply(t,n);function c(e){i(a,r,o,c,s,"next",e)}function s(e){i(a,r,o,c,s,"throw",e)}c(void 0)}))}}var c=Shopware,s=c.Component,u=c.Context;s.override("sw-profile-index-general",{template:'{% block sw_profile_index_general_password %}\n    <template v-if="!denyPasswordLogin">\n        {% parent %}\n    </template>\n\n    {% block sw_profile_index_admin_open_auth %}\n        <sw-card\n            position-identifier="heptacom-admin-open-auth-user-profile-sso"\n            :title="$tc(\'sw-profile-index.titleHeptacomAdminOpenAuthCard\')"\n            :isLoading="isUserLoading || !languageId"\n        >\n            {% block sw_profile_index_admin_open_auth_clients %}\n                <template>\n                    {% block sw_profile_index_admin_open_auth_clients_cards %}\n                        <sw-container rows="1fr">\n                            <sw-card-section\n                                v-for="client of heptacomAdminOpenAuthClients"\n                                :key="client.id"\n                                :slim="true"\n                                divider="bottom"\n                            >\n                                {% block sw_profile_index_admin_open_auth_clients_cards_item %}\n                                    <sw-container columns="1fr auto">\n                                        {% block sw_profile_index_admin_open_auth_clients_cards_item_provider %}\n                                            <div>\n                                                {{ client.name }}\n                                            </div>\n                                        {% endblock %}\n\n                                        {% block sw_profile_index_admin_open_auth_clients_cards_item_action %}\n                                            <sw-button\n                                                v-if="client.connected"\n                                                @click="revokeHeptacomAdminOpenAuthUserKey(client.id)"\n                                                icon="regular-minus"\n                                            >\n                                                {{ $t(\'sw-profile-index.heptacomAdminOpenAuth.userKeys.actions.revoke\') }}\n                                            </sw-button>\n                                            <sw-button\n                                                v-else-if="!client.connected"\n                                                @click="redirectToLoginMask(client.id)"\n                                                icon="regular-plus"\n                                            >\n                                                {{ $t(\'sw-profile-index.heptacomAdminOpenAuth.userKeys.actions.connect\') }}\n                                            </sw-button>\n                                        {% endblock %}\n                                    </sw-container>\n                                {% endblock %}\n                            </sw-card-section>\n                        </sw-container>\n                    {% endblock %}\n                </template>\n            {% endblock %}\n        </sw-card>\n    {% endblock %}\n{% endblock %}\n',inject:["repositoryFactory","systemConfigApiService"],data:function(){return{denyPasswordLogin:!1,heptacomAdminOpenAuthLoading:!0,heptacomAdminOpenAuthClients:[]}},computed:{heptacomAdminOpenAuthClientsRepository:function(){return this.repositoryFactory.create("heptacom_admin_open_auth_client")},heptacomAdminOpenAuthHttpClient:function(){return this.heptacomAdminOpenAuthClientsRepository.httpClient}},watch:{isUserLoading:{handler:function(){this.loadHeptacomAdminOpenAuth().then()}},languages:{handler:function(){this.loadHeptacomAdminOpenAuth().then()}}},created:function(){var e=this;this.systemConfigApiService.getValues("KskHeptacomAdminOpenAuth.config").then((function(t){e.denyPasswordLogin=t["KskHeptacomAdminOpenAuth.config.denyPasswordLogin"]}))},methods:{loadHeptacomAdminOpenAuth:function(){var e=this;return a(o().mark((function t(){var n,r;return o().wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(!e.isUserLoading&&e.languages){t.next=2;break}return t.abrupt("return");case 2:return e.heptacomAdminOpenAuthLoading=!0,e.heptacomAdminOpenAuthClients=[],n=e.heptacomAdminOpenAuthClientsRepository.buildHeaders(u.api),t.next=7,e.heptacomAdminOpenAuthHttpClient.get("/_admin/open-auth/client/list",{headers:n});case 7:r=t.sent,e.heptacomAdminOpenAuthClients=r.data.data,e.heptacomAdminOpenAuthLoading=!1;case 10:case"end":return t.stop()}}),t)})))()},redirectToLoginMask:function(e){var t=this;return a(o().mark((function n(){var r,i,a;return o().wrap((function(n){for(;;)switch(n.prev=n.next){case 0:return r=window.location.pathname+window.location.search+window.location.hash,i=t.heptacomAdminOpenAuthClientsRepository.buildHeaders(u.api),n.next=4,t.heptacomAdminOpenAuthHttpClient.post("/_action/open-auth/".concat(e,"/connect?redirectTo=").concat(encodeURIComponent(r)),{},{headers:i});case 4:a=n.sent,window.location.href=a.data.target;case 6:case"end":return n.stop()}}),n)})))()},revokeHeptacomAdminOpenAuthUserKey:function(e){var t=this;return a(o().mark((function n(){var r;return o().wrap((function(n){for(;;)switch(n.prev=n.next){case 0:return r=t.heptacomAdminOpenAuthClientsRepository.buildHeaders(u.api),n.next=3,t.heptacomAdminOpenAuthHttpClient.post("/_action/open-auth/".concat(e,"/disconnect"),{},{headers:r});case 3:return n.next=5,t.loadHeptacomAdminOpenAuth();case 5:case"end":return n.stop()}}),n)})))()}}});Shopware.Component.override("sw-verify-user-modal",{template:'{% block sw_settings_user_detail_content_confirm_password_modal_input__confirm_password %}\n    <heptacom-admin-open-auth-user-confirm-login\n        :divider="!denyPasswordLogin"\n        ref="heptacomAdminOpenAuthUserConfirmLogin"\n        @confirm="heptacomAdminOpenAuthUserConfirm"\n    ></heptacom-admin-open-auth-user-confirm-login>\n\n    <template v-if="!denyPasswordLogin">\n        {% parent %}\n    </template>\n{% endblock %}\n\n{% block sw_settings_user_detail_content_confirm_password_modal_actions_change %}\n    <template v-if="!denyPasswordLogin">\n        {% parent %}\n    </template>\n{% endblock %}\n',inject:["systemConfigApiService"],data:function(){return{denyPasswordLogin:!1}},created:function(){var e=this;this.systemConfigApiService.getValues("KskHeptacomAdminOpenAuth.config").then((function(t){e.denyPasswordLogin=t["KskHeptacomAdminOpenAuth.config.denyPasswordLogin"]}))},methods:{onCloseConfirmPasswordModal:function(){return this.$refs.heptacomAdminOpenAuthUserConfirmLogin.abortAuthFlow(),this.$super("onCloseConfirmPasswordModal")},heptacomAdminOpenAuthUserConfirm:function(e){this.$emit("verified",e)}}});n("KnEA");var l=Shopware,p=l.Component,d=l.Module;p.register("heptacom-admin-open-auth-client-rule-container",(function(){return n.e(7).then(n.bind(null,"BnvO"))})),p.register("heptacom-admin-open-auth-client-rule-item",(function(){return n.e(3).then(n.bind(null,"hu90"))})),p.register("heptacom-admin-open-auth-client-create-page",(function(){return n.e(4).then(n.bind(null,"LvZ1"))})),p.register("heptacom-admin-open-auth-client-edit-page",(function(){return n.e(5).then(n.bind(null,"JoBt"))})),p.register("heptacom-admin-open-auth-client-listing-page",(function(){return n.e(6).then(n.bind(null,"teqL"))})),d.register("heptacom-admin-open-auth-client",{type:"plugin",name:"heptacom-admin-open-auth-client.module.name",title:"heptacom-admin-open-auth-client.module.title",description:"heptacom-admin-open-auth-client.module.description",color:"#FFC2A2",icon:"regular-sign-in",routes:{create:{component:"heptacom-admin-open-auth-client-create-page",path:"create",meta:{parentPath:"heptacom.admin.open.auth.client.settings",privilege:"heptacom_admin_open_auth_client.creator"}},edit:{component:"heptacom-admin-open-auth-client-edit-page",path:"edit/:id",meta:{parentPath:"heptacom.admin.open.auth.client.settings",privilege:"heptacom_admin_open_auth_client.editor"},props:{default:function(e){return{clientId:e.params.id}}}},settings:{component:"heptacom-admin-open-auth-client-listing-page",path:"settings",meta:{parentPath:"sw.settings.index",privilege:"heptacom_admin_open_auth_client.viewer"}}},settingsItem:[{to:"heptacom.admin.open.auth.client.settings",group:"system",icon:"regular-sign-in",privilege:"heptacom_admin_open_auth_client.viewer"}]});Shopware.Component.override("heptacom-admin-open-auth-client-edit-page",{template:'{% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_store_user_token %}\n    <template v-if="!item || item.provider !== \'jumpcloud\'">\n        {% parent %}\n    </template>\n{% endblock %}\n\n{%  block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner %}\n    {% parent %}\n\n    <template v-if="item && item.provider === \'jumpcloud\'">\n        <sw-textarea-field\n            :disabled="true"\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate\')"\n            :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate-help\')"\n            v-model="item.config.serviceProviderCertificate"\n        ></sw-textarea-field>\n        <sw-textarea-field\n            :disabled="true"\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey\')"\n            :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey-help\')"\n            v-model="item.config.serviceProviderPublicKey"\n        ></sw-textarea-field>\n    </template>\n{% endblock %}\n\n',data:function(){return{availableProperties:["firstName","lastName","email","timezone","locale"]}},watch:{item:function(e){e&&"jumpcloud"===e.provider&&(e.config.attributeMapping||(e.config.attributeMapping={}))}}});Shopware.Component.override("heptacom-admin-open-auth-client-edit-page",{template:'{% block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner_store_user_token %}\n    <template v-if="!item || item.provider !== \'saml2\'">\n        {% parent %}\n    </template>\n{% endblock %}\n\n{%  block heptacom_admin_open_auth_client_edit_page_content_base_settings_inner %}\n    {% parent %}\n\n    <template v-if="item && item.provider === \'saml2\'">\n        <sw-textarea-field\n            :disabled="true"\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate\')"\n            :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderCertificate-help\')"\n            v-model="item.config.serviceProviderCertificate"\n        ></sw-textarea-field>\n        <sw-textarea-field\n            :disabled="true"\n            :label="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey\')"\n            :help-text="$t(\'heptacomAdminOpenAuthClient.providerFields.saml2.serviceProviderPublicKey-help\')"\n            v-model="item.config.serviceProviderPublicKey"\n        ></sw-textarea-field>\n    </template>\n{% endblock %}\n',data:function(){return{selectedMappingTemplate:null,availableProperties:["firstName","lastName","email","timezone","locale"],attributeMappingTemplates:{friendlyNames:{firstName:"givenName",lastName:"surName",email:"emailAddress"},x500:{firstName:"urn:oid:2.5.4.42",lastName:"urn:oid:2.5.4.4",email:"urn:oid:1.2.840.113549.1.9.1"},azure:{firstName:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname",lastName:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname",email:"http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress"}}}},watch:{item:function(e){e&&"saml2"===e.provider&&(e.config.attributeMapping||(e.config.attributeMapping={}))}},methods:{onApplyMappingTemplate:function(e){var t=this.attributeMappingTemplates[e];this.item.config.attributeMapping=Object.assign(this.item.config.attributeMapping,t)}}});var h=Shopware,m=h.Application,f=h.Component;function g(e){return(g="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function v(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}function y(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,(o=r.key,i=void 0,i=function(e,t){if("object"!==g(e)||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,t||"default");if("object"!==g(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(o,"string"),"symbol"===g(i)?i:String(i)),r)}var o,i}function b(e,t){return(b=Object.setPrototypeOf?Object.setPrototypeOf.bind():function(e,t){return e.__proto__=t,e})(e,t)}function _(e){var t=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(e){return!1}}();return function(){var n,r=O(e);if(t){var o=O(this).constructor;n=Reflect.construct(r,arguments,o)}else n=r.apply(this,arguments);return w(this,n)}}function w(e,t){if(t&&("object"===g(t)||"function"==typeof t))return t;if(void 0!==t)throw new TypeError("Derived constructors may only return object or undefined");return function(e){if(void 0===e)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return e}(e)}function O(e){return(O=Object.setPrototypeOf?Object.getPrototypeOf.bind():function(e){return e.__proto__||Object.getPrototypeOf(e)})(e)}f.register("heptacom-admin-open-auth-provider-cidaas-settings",(function(){return n.e(8).then(n.bind(null,"h0CI"))})),f.extend("heptacom-admin-open-auth-provider-cidaas-role-assignment","heptacom-admin-open-auth-provider-open-id-connect-role-assignment",{}),f.register("heptacom-admin-open-auth-provider-google-cloud-settings",(function(){return n.e(9).then(n.bind(null,"/7Bt"))})),f.extend("heptacom-admin-open-auth-provider-google-role-assignment","heptacom-admin-open-auth-provider-open-id-connect-role-assignment",{}),f.register("heptacom-admin-open-auth-provider-jira-settings",(function(){return n.e(10).then(n.bind(null,"LQm9"))})),f.register("heptacom-admin-open-auth-provider-jumpcloud-settings",(function(){return n.e(11).then(n.bind(null,"I0LW"))})),f.register("heptacom-admin-open-auth-provider-keycloak-settings",(function(){return n.e(12).then(n.bind(null,"qk0B"))})),f.extend("heptacom-admin-open-auth-provider-keycloak-role-assignment","heptacom-admin-open-auth-provider-open-id-connect-role-assignment",{}),f.register("heptacom-admin-open-auth-provider-microsoft-azure-oidc-settings",(function(){return n.e(13).then(n.bind(null,"gsex"))})),f.extend("heptacom-admin-open-auth-provider-okta-role-assignment","heptacom-admin-open-auth-provider-open-id-connect-role-assignment",{}),f.register("heptacom-admin-open-auth-provider-okta-settings",(function(){return n.e(14).then(n.bind(null,"jYnA"))})),f.extend("heptacom-admin-open-auth-provider-okta-role-assignment","heptacom-admin-open-auth-provider-open-id-connect-role-assignment",{}),f.register("heptacom-admin-open-auth-provider-onelogin-settings",(function(){return n.e(15).then(n.bind(null,"CIRs"))})),f.extend("heptacom-admin-open-auth-provider-onelogin-role-assignment","heptacom-admin-open-auth-provider-open-id-connect-role-assignment",{}),f.register("heptacom-admin-open-auth-provider-open-id-connect-settings",(function(){return n.e(0).then(n.bind(null,"/QCv"))})),f.register("heptacom-admin-open-auth-provider-open-id-connect-role-assignment",(function(){return n.e(0).then(n.bind(null,"/QCv"))})),f.register("heptacom-admin-open-auth-provider-saml2-settings",(function(){return n.e(16).then(n.bind(null,"8FZX"))})),m.addServiceProviderDecorator("heptacomOauthRuleDataProvider",(function(e){return e.addCondition("heptacomAdminOpenAuthSaml2Role",{component:"sw-condition-generic",label:"heptacomAdminOpenAuthClient.providerFields.saml2.condition.rule.roles",scopes:["saml2"],group:"user"}),e}));var A=Shopware.Classes.ApiService,k=function(e){!function(e,t){if("function"!=typeof t&&null!==t)throw new TypeError("Super expression must either be null or a function");e.prototype=Object.create(t&&t.prototype,{constructor:{value:e,writable:!0,configurable:!0}}),Object.defineProperty(e,"prototype",{writable:!1}),t&&b(e,t)}(i,e);var t,n,r,o=_(i);function i(e,t){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:"heptacom_admin_open_auth_provider";return v(this,i),o.call(this,e,t,n)}return t=i,(n=[{key:"list",value:function(){var e=this.getBasicHeaders();return this.httpClient.get("_action/".concat(this.getApiBasePath(),"/list"),{headers:e}).then((function(e){return A.handleResponse(e)}))}},{key:"factorize",value:function(e){var t=this.getBasicHeaders();return this.httpClient.post("_action/".concat(this.getApiBasePath(),"/factorize"),{provider_key:e},{headers:t}).then((function(e){return A.handleResponse(e)}))}},{key:"getRedirectUri",value:function(e){var t=this.getBasicHeaders();return this.httpClient.post("_action/".concat(this.getApiBasePath(),"/client-redirect-url"),{client_id:e},{headers:t}).then((function(e){return A.handleResponse(e)}))}},{key:"getMetadataUri",value:function(e){var t=this.getBasicHeaders();return this.httpClient.post("_action/".concat(this.getApiBasePath(),"/client-metadata-url"),{client_id:e},{headers:t}).then((function(e){return A.handleResponse(e)}))}}])&&y(t.prototype,n),r&&y(t,r),Object.defineProperty(t,"prototype",{writable:!1}),i}(A),C=Shopware.Application;function P(e){return(P="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e})(e)}function S(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function x(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?S(Object(n),!0).forEach((function(t){T(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):S(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function j(e,t){return function(e){if(Array.isArray(e))return e}(e)||function(e,t){var n=null==e?null:"undefined"!=typeof Symbol&&e[Symbol.iterator]||e["@@iterator"];if(null!=n){var r,o,i,a,c=[],s=!0,u=!1;try{if(i=(n=n.call(e)).next,0===t){if(Object(n)!==n)return;s=!1}else for(;!(s=(r=i.call(n)).done)&&(c.push(r.value),c.length!==t);s=!0);}catch(e){u=!0,o=e}finally{try{if(!s&&null!=n.return&&(a=n.return(),Object(a)!==a))return}finally{if(u)throw o}}return c}}(e,t)||function(e,t){if(!e)return;if("string"==typeof e)return E(e,t);var n=Object.prototype.toString.call(e).slice(8,-1);"Object"===n&&e.constructor&&(n=e.constructor.name);if("Map"===n||"Set"===n)return Array.from(e);if("Arguments"===n||/^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n))return E(e,t)}(e,t)||function(){throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.")}()}function E(e,t){(null==t||t>e.length)&&(t=e.length);for(var n=0,r=new Array(t);n<t;n++)r[n]=e[n];return r}function L(e,t){for(var n=0;n<t.length;n++){var r=t[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(e,q(r.key),r)}}function T(e,t,n){return(t=q(t))in e?Object.defineProperty(e,t,{value:n,enumerable:!0,configurable:!0,writable:!0}):e[t]=n,e}function q(e){var t=function(e,t){if("object"!==P(e)||null===e)return e;var n=e[Symbol.toPrimitive];if(void 0!==n){var r=n.call(e,t||"default");if("object"!==P(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===t?String:Number)(e)}(e,"string");return"symbol"===P(t)?t:String(t)}C.addServiceProvider("HeptacomAdminOpenAuthProviderApiService",(function(e){var t=C.getContainer("init");return new k(t.httpClient,e.loginService)}));var N=function(){function e(){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,e),T(this,"$store",{}),T(this,"operators",{lowerThanEquals:{identifier:"<=",label:"global.sw-condition.operator.lowerThanEquals"},equals:{identifier:"=",label:"global.sw-condition.operator.equals"},greaterThanEquals:{identifier:">=",label:"global.sw-condition.operator.greaterThanEquals"},notEquals:{identifier:"!=",label:"global.sw-condition.operator.notEquals"},greaterThan:{identifier:">",label:"global.sw-condition.operator.greaterThan"},lowerThan:{identifier:"<",label:"global.sw-condition.operator.lowerThan"},isOneOf:{identifier:"=",label:"global.sw-condition.operator.isOneOf"},isNoneOf:{identifier:"!=",label:"global.sw-condition.operator.isNoneOf"},gross:{identifier:!1,label:"global.sw-condition.operator.gross"},net:{identifier:!0,label:"global.sw-condition.operator.net"},empty:{identifier:"empty",label:"global.sw-condition.operator.empty"}}),T(this,"operatorSets",{defaultSet:[this.operators.equals,this.operators.notEquals,this.operators.greaterThanEquals,this.operators.lowerThanEquals],singleStore:[this.operators.equals,this.operators.notEquals],multiStore:[this.operators.isOneOf,this.operators.isNoneOf],string:[this.operators.equals,this.operators.notEquals],bool:[this.operators.equals],number:[this.operators.equals,this.operators.greaterThan,this.operators.greaterThanEquals,this.operators.lowerThan,this.operators.lowerThanEquals,this.operators.notEquals],date:[this.operators.equals,this.operators.greaterThan,this.operators.greaterThanEquals,this.operators.lowerThan,this.operators.lowerThanEquals,this.operators.notEquals],isNet:[this.operators.gross,this.operators.net],empty:[this.operators.empty],zipCode:[this.operators.greaterThan,this.operators.greaterThanEquals,this.operators.lowerThan,this.operators.lowerThanEquals]}),T(this,"groups",{general:{id:"general",name:"sw-settings-rule.detail.groups.general"},user:{id:"user",name:"heptacom-admin-open-auth.condition.group.user"}})}var t,n,r;return t=e,(n=[{key:"getByType",value:function(e){if(!e)return this.getByType("placeholder");if("scriptRule"===e){var t=this.getConditions().filter((function(e){return"scriptRule"===e.type})).shift();if(t)return t}return this.$store[e]}},{key:"getOperatorSet",value:function(e){return this.operatorSets[e]}},{key:"addEmptyOperatorToOperatorSet",value:function(e){return e.concat(this.operatorSets.empty)}},{key:"getOperatorSetByComponent",value:function(e){var t=e.config.componentName,n=e.type;return"sw-single-select"===t?this.operatorSets.singleStore:"sw-multi-select"===t?this.operatorSets.multiStore:"bool"===n?this.operatorSets.bool:"text"===n?this.operatorSets.string:"int"===n?this.operatorSets.number:this.operatorSets.defaultSet}},{key:"getOperatorOptionsByIdentifiers",value:function(e){var t=this,n=arguments.length>1&&void 0!==arguments[1]&&arguments[1];return e.map((function(e){var r=Object.entries(t.operators).find((function(t){var r=j(t,2),o=r[0],i=r[1];return!(n&&["equals","notEquals"].includes(o)||!n&&["isOneOf","isNoneOf"].includes(o)||e!==i.identifier)}));return r?r.pop():{identifier:e,label:"global.sw-condition.operator.".concat(e)}}))}},{key:"getByGroup",value:function(e){var t=Object.values(this.$store),n=[];return t.forEach((function(t){t.group===e&&n.push(t)})),n}},{key:"getGroups",value:function(){return this.groups}},{key:"upsertGroup",value:function(e,t){this.groups[e]=x(x({},this.groups[e]),t)}},{key:"removeGroup",value:function(e){delete this.groups[e]}},{key:"addCondition",value:function(e,t){var n;t.type=e,this.$store[null!==(n=t.scriptId)&&void 0!==n?n:e]=t}},{key:"getConditions",value:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:null,t=Object.values(this.$store);return null!==e&&(t=t.filter((function(t){return e.some((function(e){return-1!==t.scopes.indexOf(e)}))}))),t}},{key:"getComponentByCondition",value:function(e){if(this.isAndContainer(e))return"sw-condition-and-container";if(this.isOrContainer(e))return"sw-condition-or-container";if(this.isAllLineItemsContainer(e))return"sw-condition-all-line-items-container";if(!e.type)return"sw-condition-base";var t=this.getByType(e.type);return void 0!==t&&t.component?t.component:"sw-condition-not-found"}},{key:"getAndContainerData",value:function(){return{type:"andContainer",value:{}}}},{key:"isAndContainer",value:function(e){return"andContainer"===e.type}},{key:"getOrContainerData",value:function(){return{type:"orContainer",value:{}}}},{key:"isOrContainer",value:function(e){return"orContainer"===e.type}},{key:"getPlaceholderData",value:function(){return{type:null,value:{}}}},{key:"isAllLineItemsContainer",value:function(e){return"allLineItemsContainer"===e.type}}])&&L(t.prototype,n),r&&L(t,r),Object.defineProperty(t,"prototype",{writable:!1}),e}();Shopware.Application.addServiceProvider("heptacomOauthRuleDataProvider",(function(){return new N})).addServiceProviderDecorator("heptacomOauthRuleDataProvider",(function(e){return e.addCondition("alwaysValid",{component:"sw-condition-is-always-valid",label:"global.sw-condition.condition.alwaysValidRule",scopes:["global"],group:"general"}),e.addCondition("dateRange",{component:"sw-condition-date-range",label:"global.sw-condition.condition.dateRangeRule.label",scopes:["global"],group:"general"}),e.addCondition("timeRange",{component:"sw-condition-time-range",label:"global.sw-condition.condition.timeRangeRule",scopes:["global"],group:"general"}),e.addCondition("heptacomAdminOpenAuthEmail",{component:"sw-condition-generic",label:"heptacom-admin-open-auth.condition.rule.email",scopes:["global"],group:"user"}),e.addCondition("heptacomAdminOpenAuthTimeZone",{component:"sw-condition-generic",label:"heptacom-admin-open-auth.condition.rule.timeZone",scopes:["global"],group:"user"}),e.addCondition("heptacomAdminOpenAuthLocale",{component:"sw-condition-generic",label:"heptacom-admin-open-auth.condition.rule.locale",scopes:["global"],group:"user"}),e.addCondition("heptacomAdminOpenAuthPrimaryKey",{component:"sw-condition-generic",label:"heptacom-admin-open-auth.condition.rule.primaryKey",scopes:["global"],group:"user"}),e}))},KnEA:function(e,t){Shopware.Service("privileges").addPrivilegeMappingEntry({category:"permissions",parent:"settings",key:"heptacom_admin_open_auth_client",roles:{viewer:{privileges:["heptacom_admin_open_auth_client:read","heptacom_admin_open_auth_user_key:read","heptacom_admin_open_auth_client_rule:read","heptacom_admin_open_auth_client_rule_condition:read"],dependencies:[]},editor:{privileges:["heptacom_admin_open_auth_client:update","heptacom_admin_open_auth_client_rule:create","heptacom_admin_open_auth_client_rule:update","heptacom_admin_open_auth_client_rule:delete","heptacom_admin_open_auth_client_rule_condition:create","heptacom_admin_open_auth_client_rule_condition:update","heptacom_admin_open_auth_client_rule_condition:delete"],dependencies:["heptacom_admin_open_auth_client.viewer"]},creator:{privileges:["heptacom_admin_open_auth_client:create"],dependencies:["heptacom_admin_open_auth_client.editor"]},deleter:{privileges:["heptacom_admin_open_auth_client:delete"],dependencies:["heptacom_admin_open_auth_client.viewer"]}}})},uvv5:function(e,t,n){var r=Shopware.Component;r.register("heptacom-admin-open-auth-scope-field",(function(){return n.e(1).then(n.bind(null,"zHZc"))})),r.register("heptacom-admin-open-auth-user-confirm-login",(function(){return n.e(2).then(n.bind(null,"b/Rg"))}))}});
//# sourceMappingURL=ksk-heptacom-admin-open-auth.js.map