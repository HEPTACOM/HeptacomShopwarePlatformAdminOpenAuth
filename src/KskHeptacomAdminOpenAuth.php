<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;

final class KskHeptacomAdminOpenAuth extends Plugin
{
    public const CONFIG_DENY_PASSWORD_LOGIN = 'KskHeptacomAdminOpenAuth.config.denyPasswordLogin';

    /**
     * All plugin tables that should be removed on uninstall.
     * The tables are removed in the order they are defined here.
     */
    private const PLUGIN_TABLES = [
        'heptacom_admin_open_auth_user_token',
        'heptacom_admin_open_auth_user_key',
        'heptacom_admin_open_auth_user_email',
        'heptacom_admin_open_auth_login',
        'heptacom_admin_open_auth_client_rule_role',
        'heptacom_admin_open_auth_client_rule_condition',
        'heptacom_admin_open_auth_client_rule',
        'heptacom_admin_open_auth_client_role',
        'heptacom_admin_open_auth_client',
    ];

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

            foreach (self::PLUGIN_TABLES as $table) {
                if ($schemaManager->tablesExist($table)) {
                    $schemaManager->dropTable($table);
                }
            }
        }
    }
}
