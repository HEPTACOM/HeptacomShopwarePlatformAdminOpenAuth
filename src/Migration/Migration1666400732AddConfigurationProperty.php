<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1666400732AddConfigurationProperty extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1666400732;
    }

    public function update(Connection $connection): void
    {
        $updateConfiguration = <<<'SQL'
UPDATE `heptacom_admin_open_auth_client` SET `config` = JSON_SET(`config`, '$.id', `id`);
SQL;
        $connection->executeStatement($updateConfiguration);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
