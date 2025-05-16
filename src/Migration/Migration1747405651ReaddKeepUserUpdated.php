<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

/**
 * Re-adds the keep_user_updated column to the heptacom_admin_open_auth_client table in case database:migrate-destructive was already executed.
 *
 * @see Migration1702377314MigrateRoleAssignment at commit f33a1b7662ccb017d645e785ae05a9a504139a40
 */
class Migration1747405651ReaddKeepUserUpdated extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1747405651;
    }

    public function update(Connection $connection): void
    {
        // check if keep_user_updated column exists
        $columnExists = $connection->executeQuery(<<<SQL
            SELECT COUNT(*)
            FROM information_schema.COLUMNS
            WHERE TABLE_NAME = 'heptacom_admin_open_auth_client'
                AND COLUMN_NAME = 'keep_user_updated'
                AND TABLE_SCHEMA = DATABASE();
        SQL)->fetchOne() === 1;

        // column got deleted by faulty destructive migration
        if (!$columnExists) {
            $connection->executeStatement(<<<SQL
                ALTER TABLE `heptacom_admin_open_auth_client`
                    ADD COLUMN `keep_user_updated` BOOLEAN NOT NULL DEFAULT TRUE AFTER `store_user_token`;
            SQL);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
