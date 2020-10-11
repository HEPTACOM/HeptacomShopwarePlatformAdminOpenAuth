<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth;

use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

class KskHeptacomAdminOpenAuth extends Plugin
{
    public function boot(): void
    {
        $autoloader = \dirname(__DIR__) . '/vendor/autoload.php';

        if (\is_file($autoloader)) {
            $loader = require $autoloader;
            \spl_autoload_unregister([$loader, 'loadClass']);
            $loader->register(false);
        }
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);
        $uninstallContext->enableKeepMigrations();
    }
}
