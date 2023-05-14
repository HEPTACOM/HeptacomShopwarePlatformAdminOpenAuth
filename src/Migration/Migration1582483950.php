<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1582483950 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1582483950;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `heptacom_admin_open_auth_login` (
    `id` BINARY(16) NOT NULL,
    `client_id` BINARY(16) NOT NULL,
    `state` BINARY(16) NOT NULL,
    `user_id` BINARY(16) NULL,
    `password` VARCHAR(255) NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3),
    PRIMARY KEY (`id`),
    CONSTRAINT `fk.heptacom_admin_open_auth_login.client_id`
		FOREIGN KEY (`client_id`) REFERENCES `heptacom_admin_open_auth_client` (`id`)
			ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk.heptacom_admin_open_auth_login.user_id`
		FOREIGN KEY (`client_id`) REFERENCES `user` (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL;
        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
