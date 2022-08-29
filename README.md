# Open Authentication login for shopware platform administration
## This is part of HEPTACOM solutions for medium and large enterprise
### Shopware plugin to allow external login provider in the administration

![Packagist Version](https://img.shields.io/packagist/v/heptacom/shopware-platform-admin-open-auth?style=flat-square)
![PHP from Packagist](https://img.shields.io/packagist/php-v/heptacom/shopware-platform-admin-open-auth?style=flat-square)
[![Software License](https://img.shields.io/packagist/l/heptacom/shopware-platform-admin-open-auth?style=flat-square)](./LICENSE.md)
![GitHub code size in bytes](https://img.shields.io/github/languages/code-size/heptacom/HeptacomShopwarePlatformAdminOpenAuth?style=flat-square)
[![GitHub issues](https://img.shields.io/github/issues/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth?style=flat-square)](https://github.com/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth/issues)
[![GitHub forks](https://img.shields.io/github/forks/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth?style=flat-square)](https://github.com/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth/network)
[![GitHub stars](https://img.shields.io/github/stars/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth?style=flat-square)](https://github.com/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth/stargazers)
![GitHub watchers](https://img.shields.io/github/watchers/heptacom/HeptacomShopwarePlatformAdminOpenAuth?style=flat-square)
![Packagist](https://img.shields.io/packagist/dt/heptacom/shopware-platform-admin-open-auth?style=flat-square)

![GitHub contributors](https://img.shields.io/github/contributors/heptacom/HeptacomShopwarePlatformAdminOpenAuth?style=flat-square)
![GitHub commit activity](https://img.shields.io/github/commit-activity/y/heptacom/HeptacomShopwarePlatformAdminOpenAuth?style=flat-square)

This Shopware 6 plugin allows to add "Login with" functionality into the Shopware administration login page and password confirmation dialogs.


## Supported providers

### Atlassian Jira

<img alt="Atlassian Jira" height="64" src="./src/Resources/app/administration/static/logo/jira_logo.svg"/>

Use with Atlassian Jira.
Read more [here](https://developer.atlassian.com/cloud/jira/platform/oauth-2-3lo-apps/#enabling-oauth-2-0--3lo-).


### cidaas

<img alt="cidaas" height="64" src="./src/Resources/app/administration/static/logo/cidaas_logo.svg"/>

Use with cidaas IAM service.
Read more [here](https://docs.cidaas.com/create-application/createapplication.html).


### Google Cloud

<img alt="Google Cloud" height="64" src="./src/Resources/app/administration/static/logo/google_logo.svg"/>

Use with Google Identity service.
Read more [here](https://developers.google.com/identity/protocols/oauth2/openid-connect).


### Keycloak

<img alt="Keycloak" height="64" src="./src/Resources/app/administration/static/logo/keycloak_logo.svg"/>

Use your own identity service with [keycloack](https://www.keycloak.org/).
Read more [here](https://blogs.sap.com/2021/08/23/keyclock-as-an-openid-connect-oidc-provider./).


### Microsoft Azure

<img alt="Microsoft Azure" height="64" src="./src/Resources/app/administration/static/logo/microsoft_logo.svg"/>

Use with Microsoft Azure Active Directory.
Read more [here](https://docs.microsoft.com/en-US/azure/active-directory/develop/quickstart-register-app).


### Okta

<img alt="Okta" height="64" src="./src/Resources/app/administration/static/logo/okta_logo.png"/>

Use with Okta Workforce Identity.
Read more [here](https://help.okta.com/en-us/Content/Topics/Apps/Apps_App_Integration_Wizard_OIDC.htm).


### OneLogin

<img alt="OneLogin" height="64" src="./src/Resources/app/administration/static/logo/onelogin_logo.svg"/>

Use with onelogin Workforce Identity.
Read more [here](https://developers.onelogin.com/blog/how-to-use-openid-connect-authentication-with-dotnet-core#heading-menu).


### OpenID Connect

<img alt="OpenID Connect" height="64" src="./src/Resources/app/administration/static/logo/openid_logo.svg"/>

Try any OpenID Connect provider, that we did not explicitly prepare an optimized configuration for.


## Changes

View the [CHANGELOG](./CHANGELOG.md) file attached to this project.

## License

Copyright 2020 HEPTACOM GmbH

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this project except in compliance with the License.
You may obtain a copy of the License at [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0) or see the [local copy](./LICENSE.md).

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and limitations under the License.
