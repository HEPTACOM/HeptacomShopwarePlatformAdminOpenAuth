<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1661868779FlagKeepUserUpdated extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1661868779;
    }

    public function update(Connection $connection): void
    {
        $alterTable = <<<'SQL'
ALTER TABLE
    `heptacom_admin_open_auth_client`
ADD COLUMN `keep_user_updated` BOOLEAN NOT NULL DEFAULT TRUE AFTER `user_become_admin`;
SQL;
        $connection->executeStatement($alterTable);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
