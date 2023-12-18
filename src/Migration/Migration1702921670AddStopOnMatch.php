<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1702921670AddStopOnMatch extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1702921670;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<'SQL'
            ALTER TABLE `heptacom_admin_open_auth_client_rule`
                ADD COLUMN `stop_on_match` BOOLEAN NOT NULL DEFAULT TRUE AFTER `user_become_admin`;

            ALTER TABLE `heptacom_admin_open_auth_client_rule`
                ALTER COLUMN `user_become_admin` DROP DEFAULT;
SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
