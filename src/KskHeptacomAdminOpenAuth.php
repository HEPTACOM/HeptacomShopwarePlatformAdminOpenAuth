<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth;

use Composer\Autoload\ClassLoader;
use Doctrine\DBAL\Connection;
use Heptacom\OpenAuth\SymfonyBundle;
use Shopware\Core\Framework\Parameter\AdditionalBundleParameters;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

final class KskHeptacomAdminOpenAuth extends Plugin
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

    public function executeComposerCommands(): bool
    {
        return true;
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        /** @var Connection $connection */
        $connection = $this->container->get(Connection::class);

        if (!$uninstallContext->keepUserData()) {
            $schemaManager = $connection->createSchemaManager();
            $schemaManager->dropTable('heptacom_admin_open_auth_user_email');
            $schemaManager->dropTable('heptacom_admin_open_auth_user_key');
            $schemaManager->dropTable('heptacom_admin_open_auth_user_token');
            $schemaManager->dropTable('heptacom_admin_open_auth_login');
            $schemaManager->dropTable('heptacom_admin_open_auth_client');
        }
    }
}
