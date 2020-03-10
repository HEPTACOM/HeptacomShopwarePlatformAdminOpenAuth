<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1583709900 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1583709900;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<'SQL'
ALTER TABLE
    `heptacom_admin_open_auth_user_token`
ADD COLUMN `expires_at` DATETIME(3) NULL AFTER `refresh_token`;
SQL;
        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
