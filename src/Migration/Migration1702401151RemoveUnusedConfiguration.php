<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1702401151RemoveUnusedConfiguration extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1702401151;
    }

    public function update(Connection $connection): void
    {
        // deprecated since v3.0.2 and originally with scheduled removal in v5.0.0, therefore this is not done in the updateDestructive method
        $connection->executeStatement(<<<'SQL'
            UPDATE `heptacom_admin_open_auth_client`
            SET `config` = JSON_REMOVE(`config`, '$.redirectUri');
SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
