<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1583709702 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1583709702;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<'SQL'
UPDATE
    `heptacom_admin_open_auth_client`
SET
    `store_user_token` = JSON_EXTRACT(config, '$.storeToken')
WHERE
    `provider` = 'jira';

UPDATE
    `heptacom_admin_open_auth_client`
SET
    `config` = JSON_REMOVE(config, '$.storeToken')
WHERE
    `provider` = 'jira';
SQL;
        $connection->executeQuery($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
