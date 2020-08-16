* Extract ClientFactoryContract from ClientLoader
* Move ClientProviderRepositoryInterface into Heptacom\OpenAuth\ClientProvider\Contract namespace and used Contract pattern
* Move ClientProviderInterface into Heptacom\OpenAuth\ClientProvider\Contract namespace and used Contract pattern
* Move ClientInterface into Heptacom\OpenAuth\Client\Contract namespace and used Contract pattern
* Add RedirectBehaviour class to control redirect process
* Move TokenPairFactory into Heptacom\OpenAuth\Struct namespace
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
