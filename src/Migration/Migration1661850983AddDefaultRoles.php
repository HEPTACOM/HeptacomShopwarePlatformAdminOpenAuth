<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1661850983AddDefaultRoles extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1661850983;
    }

    public function update(Connection $connection): void
    {
        $createTable = <<<'SQL'
CREATE TABLE IF NOT EXISTS `heptacom_admin_open_auth_client_role` (
    `client_id` BINARY(16) NOT NULL,
    `acl_role_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`client_id`, `acl_role_id`),
    CONSTRAINT `fk.heptacom_admin_open_auth_client_role.client_id`
        FOREIGN KEY (`client_id`) REFERENCES `heptacom_admin_open_auth_client` (`id`)
            ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk.heptacom_admin_open_auth_client_role.acl_role_id`
        FOREIGN KEY (`acl_role_id`) REFERENCES `acl_role` (`id`)
            ON UPDATE CASCADE ON DELETE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($createTable);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
