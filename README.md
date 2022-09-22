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

## Features

* login to Shopware 6 administration using an external identity provider (IDP)
* various providers already preconfigured - Microsoft, Google, Okta, Keycloak, ...
* support for third-party IDPs supporting OpenID Connect
  * easy setup using the provider's metadata document (`.well-known/openid-configuration`)
* promote users automatically to administrators

## Security

The login to the Shopware administration is a critical part.
Security vulnerabilities in this part allow attackers access to the whole shop.

Therefore, we check our plugin critically for potential risks before merging pull requests.

In addition, our OpenId Connect implementation also checks the signature of JWT tokens, whenever possible.
When using a pre-configured OpenID Connect provider or when providing a OIDC metadata document,
the JWKS keys are automatically fetched from the IDP.

## Supported providers

We support a variety of identity providers out of the box.
If your identity provider is not listed below but offers OpenID Connect support, you can configure it manually using the OpenID Connect provider.
In any other case feel free to create a pull request.

| Provider                                                                                                                                                    |  supports language sync  |  supports timezone sync  | more info                                                                                                                      |
|-------------------------------------------------------------------------------------------------------------------------------------------------------------|:------------------------:|:------------------------:|--------------------------------------------------------------------------------------------------------------------------------|
| Atlassian Jira<br><img alt="Atlassian Jira" height="25" src="./src/Resources/app/administration/static/logo/jira_logo.svg"/>                                |            ❌             |            ✅             | Read more [here](https://developer.atlassian.com/cloud/jira/platform/oauth-2-3lo-apps/#enabling-oauth-2-0--3lo-).              |
| cidaas<br><img alt="cidaas" height="25" src="./src/Resources/app/administration/static/logo/cidaas_logo.svg"/>                                              |            ❌             |            ❌             | Read more [here](https://docs.cidaas.com/create-application/createapplication.html).                                           |
| Google Cloud<br><img alt="Google Cloud" height="25" style="margin: 25px 0;" src="./src/Resources/app/administration/static/logo/google_logo.svg"/>          |            ✅             |            ❌             | Read more [here](https://developers.google.com/identity/protocols/oauth2/openid-connect).                                      |
| [Keycloack](https://www.keycloak.org/)<br><img alt="Keycloak" height="25" src="./src/Resources/app/administration/static/logo/keycloak_logo.svg"/>          |            ✅             | depends on configuration | Read more [here](https://blogs.sap.com/2021/08/23/keyclock-as-an-openid-connect-oidc-provider./).                              |
| Microsoft Azure<br><img alt="Microsoft Azure" height="25" style="margin: 12px 0;" src="./src/Resources/app/administration/static/logo/microsoft_logo.svg"/> |            ❌             |            ❌             | Read more [here](https://docs.microsoft.com/en-US/azure/active-directory/develop/quickstart-register-app).                     |
| Okta<br><img alt="Okta" height="25" src="./src/Resources/app/administration/static/logo/okta_logo.png"/>                                                    |            ✅             |            ✅             | Read more [here](https://help.okta.com/en-us/Content/Topics/Apps/Apps_App_Integration_Wizard_OIDC.htm).                        |
| OneLogin<br><img alt="OneLogin" height="25" src="./src/Resources/app/administration/static/logo/onelogin_logo.svg"/>                                        |            ✅             |            ❌             | Read more [here](https://developers.onelogin.com/blog/how-to-use-openid-connect-authentication-with-dotnet-core#heading-menu). |
| OpenID Connect<br><img alt="OpenID Connect" height="25" src="./src/Resources/app/administration/static/logo/openid_logo.svg"/>                              | depends on configuration | depends on configuration | Try any OpenID Connect provider, that we did not explicitly prepare an optimized configuration for.                            |

## Changes

View the [CHANGELOG](./CHANGELOG.md) file attached to this project.

## Contributing

Thank you for considering contributing to this package! Be sure to sign the [CLA](CLA.md) after creating the pull request. [![CLA assistant](https://cla-assistant.io/readme/badge/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth)](https://cla-assistant.io/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth)

## License

Copyright 2020 HEPTACOM GmbH

Licensed under the Apache License, Version 2.0 (the "License"); you may not use this project except in compliance with the License.
You may obtain a copy of the License at [http://www.apache.org/licenses/LICENSE-2.0](http://www.apache.org/licenses/LICENSE-2.0) or see the [local copy](./LICENSE.md).

Unless required by applicable law or agreed to in writing, software distributed under the License is distributed on an "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and limitations under the License.

## Trademarks and Logos

All logos, available in this project are protected under copyright.
Most of them also are registered trademarks.
Therefore, the usage is only permitted when corresponding trademark/branding guidelines are fulfilled.
You can find an archived link to these guidelines below.

### Atlassian Jira

* [https://atlassian.design/foundations/logos](https://web.archive.org/web/20220826095450/https://atlassian.design/foundations/logos/)
* [https://atlassian.design/license](https://web.archive.org/web/20220826095427/https://atlassian.design/license/)

### cidaas

* [https://www.cidaas.com/branding/](https://web.archive.org/web/20220826173916/https://www.cidaas.com/branding/)

### Google

* [https://developers.google.com/identity/branding-guidelines](https://web.archive.org/web/20220826095610/https://developers.google.com/identity/branding-guidelines)
* [https://about.google/brand-resource-center/brand-elements/](https://web.archive.org/web/20220407015455/https://about.google/brand-resource-center/brand-elements/)
* [https://about.google/brand-resource-center/rules/](https://web.archive.org/web/20220318020455/https://about.google/brand-resource-center/rules/)
* [https://about.google/brand-resource-center/brand-terms/](https://web.archive.org/web/20220403110312/https://about.google/brand-resource-center/brand-terms/)

### Keycloak

* [https://design.jboss.org/keycloak/index.htm](https://design.jboss.org/keycloak/index.htm)
* [https://www.jboss.org/trademarks.html](https://web.archive.org/web/20220826095334/https://www.jboss.org/trademarks.html)

### Microsoft Azure

* [https://docs.microsoft.com/azure/active-directory/develop/howto-add-branding-in-azure-ad-apps](https://web.archive.org/web/20220826095537/https://docs.microsoft.com/en-us/azure/active-directory/develop/howto-add-branding-in-azure-ad-apps)

### Okta

* [https://www.okta.com/terms-of-use-for-okta-content/](https://web.archive.org/web/20220826163845/https://www.okta.com/terms-of-use-for-okta-content/)

### OneLogin

The One Identity logo is a registered trademark of One Identity, Inc.

* [https://www.oneidentity.com/docs/one-identity-trademark-usage-guidelines-legal-142035.pdf](https://web.archive.org/web/20220826194021/https://www.oneidentity.com/docs/one-identity-trademark-usage-guidelines-legal-142035.pdf)

### OpenID Connect

* [https://openid.net/ipr/openid-logo-guidelines.pdf](https://web.archive.org/web/20220826095703/https://openid.net/ipr/openid-logo-guidelines.pdf)
* [https://openid.net/wordpress-content/uploads/2017/06/OIDF-Policy-Trademark-Usage-Policy-Final-6-19-2017.pdf](https://web.archive.org/web/20220826100058/https://openid.net/wordpress-content/uploads/2017/06/OIDF-Policy-Trademark-Usage-Policy-Final-6-19-2017.pdf)
