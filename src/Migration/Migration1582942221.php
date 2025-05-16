<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1582942221 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1582942221;
    }

    public function update(Connection $connection): void
    {
    }

    public function updateDestructive(Connection $connection): void
    {
        $isMigrationObsolete = $connection->executeQuery("
            SELECT `class`
            FROM `migration`
            WHERE `class` = 'Heptacom\\\\AdminOpenAuth\\\\Migration\\\\Migration1583830534' AND `update` IS NOT NULL;
        ")->rowCount() === 1;

        if ($isMigrationObsolete) {
            return;
        }

        $connection->executeStatement('ALTER TABLE `heptacom_admin_open_auth_login` DROP COLUMN `password`');
    }
}
