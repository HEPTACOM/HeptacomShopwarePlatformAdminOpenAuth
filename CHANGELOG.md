# Unreleased

## Added
* Add OpenID Connect provider
* Add Google Cloud provider
* Refactor Microsoft Azure provider to use integrated OpenID Connect provider, instead of the `thenetworg/oauth2-azure` library

## Deprecated
* Microsoft Azure provider now requires another configuration parameter `tenantId`. After upgrade this parameter needs to be set. Otherwise, users might not be able to log in using Azure. The default setting after the upgrade will be `organizations`, but it might not be supported in all use-cases.

## Removed
* Drop Shopware 6.4.0 to 6.4.10 support
* Resolve issues with other plugins, also extending `@Administration/administration/index.html.twig`

## Fixed
* Fix Shopware 6.4.11 compatibility

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
