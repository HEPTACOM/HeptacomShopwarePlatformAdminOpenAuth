<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth;

use Composer\Autoload\ClassLoader;
use Heptacom\OpenAuth\SymfonyBundle;
use Shopware\Core\Framework\Parameter\AdditionalBundleParameters;
use Shopware\Core\Framework\Plugin;

class KskHeptacomAdminOpenAuth extends Plugin
{
    private static ?ClassLoader $dependencyClassLoader = null;

    public function getAdditionalBundles(AdditionalBundleParameters $parameters): array
    {
        $autoloader = \dirname(__DIR__) . '/vendor/autoload.php';

        if (\is_file($autoloader) && !self::$dependencyClassLoader instanceof ClassLoader) {
            self::$dependencyClassLoader = require $autoloader;

            if (self::$dependencyClassLoader instanceof ClassLoader) {
                \spl_autoload_unregister([self::$dependencyClassLoader, 'loadClass']);
                self::$dependencyClassLoader->register(false);
            }
        }

        $result = parent::getAdditionalBundles($parameters);
        $result[] = new SymfonyBundle();

        return $result;
    }
}
