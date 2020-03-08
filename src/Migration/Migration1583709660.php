<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1583709660 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1583709660;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<'SQL'
UPDATE
    `heptacom_admin_open_auth_client`
SET
    `config` = JSON_INSERT(
        JSON_REMOVE(config, '$.appId'),
        '$.clientId',
        JSON_EXTRACT(config, '$.appId')
    )
WHERE
    `provider` = 'microsoft_azure';
SQL;
        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
