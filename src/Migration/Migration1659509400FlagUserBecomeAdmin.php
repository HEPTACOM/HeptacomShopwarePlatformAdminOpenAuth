<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1659509400FlagUserBecomeAdmin extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1659509400;
    }

    public function update(Connection $connection): void
    {
        $alterTable = <<<'SQL'
ALTER TABLE
    `heptacom_admin_open_auth_client`
ADD COLUMN `user_become_admin` BOOLEAN NOT NULL DEFAULT TRUE AFTER `store_user_token`;
SQL;
        $connection->executeStatement($alterTable);

        $connection->transactional(static fn (Connection $connection) => $connection->update('heptacom_admin_open_auth_client', ['user_become_admin' => true]));
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
