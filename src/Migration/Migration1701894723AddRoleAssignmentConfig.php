<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1701894723AddRoleAssignmentConfig extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1701894723;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<'SQL'
UPDATE `heptacom_admin_open_auth_client`
SET `config` = JSON_SET(`config`, '$.roleAssignment', CAST('"static"' as json))
WHERE
    JSON_EXTRACT(`config`, '$.roleAssignment') IS NULL;
SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
