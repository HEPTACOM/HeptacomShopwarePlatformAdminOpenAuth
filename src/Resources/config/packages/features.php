<?php

use Heptacom\AdminOpenAuth\Http\Route\ClientRedirectRoute;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Config\Shopware\Feature\FlagsConfig;
use Symfony\Config\ShopwareConfig;

return static function (
    ContainerBuilder $container,
    ShopwareConfig $shopwareConfig,
): void {
    /** @var FlagsConfig $flag */
    $flag = $shopwareConfig
        ->feature()
        ->flags(ClientRedirectRoute::FEATURE_HEPTACOM_OPEN_AUTH_SSO_LOG_ATTEMPTS_TO_SENTRY);
    $flag
        ->default(false)
        ->major(true)
        ->toggleable(false)
        ->description('Log single sign-on attempts to Sentry');
};
