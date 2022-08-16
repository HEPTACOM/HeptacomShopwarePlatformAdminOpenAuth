<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1660654858 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1660654858;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<SQL
UPDATE `heptacom_admin_open_auth_client` SET `config` = JSON_INSERT(`config`, '$.tenant_id', 'common') WHERE `provider` = 'microsoft_azure';
SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
