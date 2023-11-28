<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1583709659 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1583709659;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<'SQL'
UPDATE
    `heptacom_admin_open_auth_client`
SET
    `config` = JSON_INSERT(
        JSON_REMOVE(config, '$.appSecret'),
        '$.clientSecret',
        JSON_EXTRACT(config, '$.appSecret')
    )
WHERE
    `provider` = 'jira';
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
