<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1685517454AddLoginRestrictions extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1685517454;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<'SQL'
ALTER TABLE `heptacom_admin_open_auth_login`
    ADD COLUMN `type` VARCHAR(255) NOT NULL DEFAULT 'login' AFTER `payload`,
    ADD COLUMN `expires_at` DATETIME(3) NULL DEFAULT NULL AFTER `type`;
SQL);

        $connection->executeStatement(<<<'SQL'
UPDATE `heptacom_admin_open_auth_login` SET `expires_at` = DATE_ADD(`created_at`, INTERVAL 10 MINUTE);
SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
