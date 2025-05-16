# Unreleased

# 8.0.0

**Added**

* Added extensible login actions to allow custom actions after a successful login
* Added `bcmath` and `gmp` to suggested php extensions in `composer.json` (GitHub Issue #5)
* Added configuration to automatically redirect users to the identity provider for login
* Added ID-Token condition for OpenID Connect based providers (GitHub Issue #34)

**Changed**

* Refactored role assignment rules to use the new login actions
* Replaced deprecated JWT libraries (`web-token/jwt-core`, `web-token/jwt-signature*`) with replacement library (`web-token/jwt-library`) (GitHub Issue #29)
* Changed user creation to create new users without admin privileges. The privileges are applied later in the login process. (GitHub Issue #12)

# 7.0.2

**Changed**

* Changed default value for `keepUserUpdated` to `true` in the client configuration, applied from [6.0.5](#605)

**Fixed**

* Fixed a bug causing the login process to terminate in some cases, after the redirect from the identity provider back to Shopware (GitHub Issues #26, #28, #31)
* Fixed broken installations when after execution of `database:migrate-destructive`, applied from [6.0.4](#604) and [6.0.5](#605) (GitHub Issue #36) 

# 7.0.1

**Added**

* Added authenticated OData request condition for OpenID Connect based providers

**Fixed**

* Fixed false negative validations for group ids condition in Microsoft Entra ID provider if too many groups are assigned to a user in Entra ID (GitHub Issue #27)
* Changed field type for additional scopes in oauth based providers to fix scopes not being saved. (GitHub issue #33)
* Fixed incomplete implementation in `\Heptacom\AdminOpenAuth\Exception\UserMismatchException` (GitHub issue #33)

# 7.0.0

**Added**

* Added Shopware 6.6 compatibility with related dependencies

**Changed**

* Renamed Microsoft Azure to Microsoft Entra ID (only translations for now; see deprecations)

**Removed**

* Removed Shopware 6.5 compatibility

**Deprecated**

* The Microsoft Azure provider will be technically renamed to Microsoft Entra ID in version 8.0.0

# 6.0.5

**Fixed**

* Fixed deleted `keep_user_updated` column in case of `database:migrate-destructive` (GitHub Issue #36)

# 6.0.4

*This version was revoked due to a bug. Please use 6.0.5 or later instead.*

**Added**

* Added authenticated OData request condition for OpenID Connect based providers (copied from 7.0.0)

**Fixed**

* Fixed false negative validations for group ids condition in Microsoft Entra ID provider if too many groups are assigned to a user in Entra ID (GitHub Issue #27, copied from 7.0.0)
* Fixed a bug causing the login process to terminate in some cases, after the redirect from the identity provider back to Shopware (GitHub Issues #26, #28, #31)
* Fixed broken installations when after execution of `database:migrate-destructive` (GitHub Issue #36)

# 6.0.3

**Fixed**

* Fixed compatibility issue with Shopware 6.5.8.8. See [Johannes's contribution on GitHub](https://github.com/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth/pull/23)

# 6.0.2

**Fixed**

* Amend typo in German snippets `heptacomAdminOpenAuthClient.providerFields.microsoft_azure_oidc.clientSecret` and `heptacomAdminOpenAuthClient.providerFields.jira.clientSecret`. See [Niklas Wolf's contribution on GitHub](https://github.com/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth/pull/21)
* Fixed Github issue #20 with Shopware provided URL field changing inputs in the configuration by using a custom URL field to allow usage of [goauthentik.io](https://goauthentik.io)

# 6.0.1

**Removed**

* Removed Symfony dependencies in the plugin `composer.json`, as these are already included in the Shopware core.

# 6.0.0

**Added**

* Added dynamic role assignment using configurable rules for clients
* Added roles to attribute mapping in SAML2 and JumpCloud provider
* Added authenticated request condition for OpenID Connect based providers
* Added group ids condition to Microsoft Azure OIDC provider
* Added `User.Read` scope to Microsoft Azure OIDC provider. This is required for the group ids condition to work.

**Fixed**

* Fixed issue in migration `Migration1685517455SetExpiredAndTypeToRequiredFields` causing issues in MariaDB installations
* Fixed update of assigned roles when `keepUserUpdated` is active

**Removed**

* Removed the static role assignment for clients. The static assignment will be automatically migrated to a rule for the dynamic assignment.
* Removed the config value `redirectUri` that is deprecated since [v3.0.2](#302) and was originally scheduled for removal in [v5.0.0](#500)

# 5.0.0

**Added**

* Added Shopware 6.5 compatibility with related dependencies
* Added download metadata button to `heptacom-admin-open-auth-client-edit-page` component
* Added configuration option `requestedAuthnContext` to SAML2 provider
* Added plugin configuration option `denyPasswordLogin` to disable password login #14
* Added popup block check for user confirmation modals, that allows more seamless confirmation flow

**Changed**

* Changed provider settings to have it's own component instead of overwriting `heptacom-admin-open-auth-client-edit-page`
* Changed `view/administration/index-js.html.twig` to only check for relevant route matches
* Changed `view/administration/index-js.html.twig` to allow login in `sw-inactivity-login`
* Changed login payload to store the requested redirect url
* Changed loading of custom Admin Vue components to asynchronous loading
* Changed SAML provider to disable the requested authn context by default

**Removed**

* Removed Shopware 6.4 compatibility
* Removed dependency `heptacom/open-auth` and ship its components with the plugin
* Removed Microsoft Azure (non OIDC) and the required dependency `thenetworg/oauth2-azure`
* Removed `\Heptacom\AdminOpenAuth\Contract\TokenRefresherInterface` and implementation `\Heptacom\AdminOpenAuth\Service\TokenRefresher`

**Security**

* Prevent confirmation of another allowed user of the same identity provider, that is not the same user as the currently logged in one, and therefore prevent follow up confirmation and actions

# 4.3.0-beta.2

**Fixed**

* Fixed `administration.heptacom.admin_open_auth.confirm` route by setting a default value for `expiresAt`

# 4.3.0-beta.1

**Added**

* Added `type` to login states to allow different behaviour per intended action
* Added `expiresAt` to login states to remove unused login states after their underlying `authorization_code` should have expired already
* Added `LoginsCleanupTask` to cleanup expired login states

**Fixed**

* Fixed usage of DBAL typed payload for non-admin users. See [AndreasA's contribution on GitHub](https://github.com/HEPTACOM/HeptacomShopwarePlatformAdminOpenAuth/pull/8)

# 4.2.1

* Fixed a bug causing the role assignment to fail and throw an exception

# 4.2.0

* Added client configuration to assign default roles to new non admin users
* Added client configuration to always update the user on login with data from the IDP
* Changed `views/administration/heptacom-admin-open-auth/page/confirm.html.twig` to immediately close the window after local storage item was set
* Changed `Heptacom\AdminOpenAuth\Service\UserResolver` and `Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient::getUser` to add more data to the user (e.g. locale and timezone)
* Fixed `Heptacom\AdminOpenAuth\Controller\AdministrationController::createClient` issues by adding default values in `Heptacom\AdminOpenAuth\Database\ClientDefinition`
* Changed composer dependency constraint of "thenetworg/oauth2-azure" from "^1.4" to "^1.4 | ^2.0" to support projects with PHP 8.0 as minimum version (thanks to Hans Höchtl @hhoechtl)
* Added generic SAML2 provider
* Added JumpCloud provider

# 4.1.0

**Added**

* Add flag to client configurations to disable users role elevation to admin
* Add OpenID Connect provider
* Add Microsoft Azure OIDC provider, using the OpenID Connect provider instead of the external `thenetworg/azure-oauth2` library
* Add Google Cloud provider
* Add Keycloak provider
* Add OneLogin provider
* Add Okta provider
* Add Cidaas provider
* Add help link to `heptacom-admin-open-auth-client-edit-page`
* Add Vue user verification component `heptacom-admin-open-auth-user-confirm-login` to build own user confirmed actions
* Add support verify using OAuth when asked for a password to verify in `sw-verify-user-modal` (generic password confirm component) and `sw-profile-index` (own profile), but not in `sw-users-permissions-user-listing` (admin user listing) as the deletion confirmation is not safely replaceable
* Add ACL for OAuth admins

**Changed**

* Changed create provider page to get a better overview of the existing providers
* Changed `\Heptacom\AdminOpenAuth\Service\OpenAuthenticationFlow::getLoginRoutes` to sort the clients by name
* Changed `sw-profile-index` overwrite to allow changes for connected OAuth clients if users only have `user_change_me` permission

**Deprecated**

* Microsoft Azure will be replaced by the Microsoft Azure OIDC provider in version 5.0.

**Removed**

* Drop Shopware 6.4.0 to 6.4.10 support

**Fixed**

* Fix Shopware 6.4.11 compatibility
* Resolve issues with other plugins, also extending `@Administration/administration/index.html.twig`
* Fix issue that users connected with this plugin cannot be deleted
* Fix display issues for checkbox icons placed in tables that are used outside of this plugin's scope

# 4.0.2

* Fix Shopware 6.4.3 compatibility

# 4.0.1

* Fix Shopware 6.4 compatibility

# 4.0.0

* Add Shopware 6.4 compatibility
* Drop Shopware 6.2 and 6.3 support

# 3.0.3

* Fix bug in Microsoft Azure client when no redirect URI has been given within the redirection steps, Microsoft used the latest registered one to redirect the user. No login happened as a different login form has been presented

# 3.0.2

* Fix bug in Microsoft Azure client when accounts in the Active Directory without Outlook mailing subscription assignment tried to login
* Amend API usage on user connection to always provide an HTTP authentication header
* Amend display of user connections as no longer client types but their names are used
* Fix bug that uncleaned installations are not able to process the configuration `redirectUri`

# 3.0.1

* Fix bug that the extracted resources are not available on plugin installation

# 3.0.0

* Remove duplicate entry in the settings overview
* Fix bug on ZIP-Installations where external dependencies were not loaded
* RedirectURL is now generated automatically to simplify domain changes
* Extract OpenAuth code contracts in new repository heptacom/open-auth
* Add method in ClientContract to authorize API requests 
* Extract ClientFactoryContract from ClientLoader
* Move ClientProviderRepositoryInterface into Heptacom\OpenAuth\ClientProvider\Contract namespace and used Contract pattern
* Move ClientProviderInterface into Heptacom\OpenAuth\ClientProvider\Contract namespace and used Contract pattern
* Move ClientInterface into Heptacom\OpenAuth\Client\Contract namespace and used Contract pattern
* Add RedirectBehaviour class to control redirect process
* Move TokenPairFactory into Heptacom\OpenAuth\Token\Contract namespace and used Contract pattern
* Move TokenPairStruct into Heptacom\OpenAuth\Struct namespace and removed Shopware dependency
* Move UserStruct into Heptacom\OpenAuth\Struct namespace and removed Shopware dependency

# 2.0.0

* Fix typo in administration template registration
* Use make manage project
* Change license from MIT to Apache 2.0
* Rename technical name of plugin to match store rules
* Make plugin compatible to a breaking change in 6.2.3 behoben (Issue NEXT-9240)
* Let login button look similar when focused like other buttons in the login form

# 1.0.2

* Fix missing settings item in administration
* Fix error on token refresh when no new refresh token is provided

# 1.0.1

* Fix bug where migrations were deleted on uninstallation
* Fix login bug as wrong DAL field keys were used 

# 1.0.0

* Add flags to enable clients for logging in and connecting
* Add connect button in administration user profile
* Add configuration in administration
* Add option to revoke connections in personal user profile
* Add custom login grant
* Add Microsoft provider
* Add Atlassian provider
* Add token storage
* Add authorized http client to easily access remote APIs
