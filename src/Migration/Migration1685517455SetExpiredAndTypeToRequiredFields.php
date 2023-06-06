<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1685517455SetExpiredAndTypeToRequiredFields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1685517455;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<'SQL'
UPDATE `heptacom_admin_open_auth_login` SET `expires_at` = DATE_ADD(`created_at`, INTERVAL 10 MINUTE) WHERE `expires_at` IS NULL;
SQL);

        $connection->executeStatement(<<<'SQL'
ALTER TABLE `heptacom_admin_open_auth_login`
    MODIFY COLUMN `expires_at` DATETIME(3) NOT NULL;
SQL);

        $connection->executeStatement(<<<'SQL'
UPDATE `heptacom_admin_open_auth_login` SET `type` = 'login' WHERE `type` IS NULL;
SQL);

        $connection->executeStatement(<<<'SQL'
ALTER TABLE `heptacom_admin_open_auth_login`
    MODIFY COLUMN `type` VARCHAR(255) NOT NULL;
SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
