<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1583709700 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1583709700;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<'SQL'
ALTER TABLE
    `heptacom_admin_open_auth_client`
    ADD COLUMN `store_user_token` BOOLEAN NOT NULL DEFAULT TRUE AFTER `connect`;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
