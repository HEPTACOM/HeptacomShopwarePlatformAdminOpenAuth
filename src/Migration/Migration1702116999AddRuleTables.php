<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1702116999AddRuleTables extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1702116999;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<'SQL'
CREATE TABLE IF NOT EXISTS `heptacom_admin_open_auth_client_rule` (
    `id` BINARY(16) NOT NULL PRIMARY KEY,
    `client_id` BINARY(16) NOT NULL,
    `user_become_admin` BOOLEAN NOT NULL DEFAULT FALSE,
    `position` INT(11) DEFAULT 0 NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    CONSTRAINT `fk.heptacom_admin_open_auth_client_rule.client_id` FOREIGN KEY (`client_id`)
        REFERENCES `heptacom_admin_open_auth_client` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL);

        $connection->executeStatement(<<<'SQL'
CREATE TABLE IF NOT EXISTS `heptacom_admin_open_auth_client_rule_condition` (
    `id` BINARY(16) NOT NULL PRIMARY KEY,
    `type` VARCHAR(255) NOT NULL,
    `client_rule_id` BINARY(16) NOT NULL,
    `parent_id` BINARY(16) NULL,
    `value` JSON NULL,
    `position` INT(11) DEFAULT 0 NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    CONSTRAINT `json.heptacom_admin_open_auth_client_rule_condition.value` CHECK (JSON_VALID (`value`)),
    CONSTRAINT `fk.heptacom_admin_open_auth_client_rule_condition.client_rule_id` FOREIGN KEY (`client_rule_id`)
        REFERENCES `heptacom_admin_open_auth_client_rule` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk.heptacom_admin_open_auth_client_rule_condition.parent_id` FOREIGN KEY (`parent_id`)
        REFERENCES heptacom_admin_open_auth_client_rule_condition (`id`) ON DELETE CASCADE ON UPDATE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL);

        $connection->executeStatement(<<<'SQL'
CREATE TABLE IF NOT EXISTS `heptacom_admin_open_auth_client_rule_role` (
    `client_rule_id` BINARY(16) NOT NULL,
    `acl_role_id` BINARY(16) NOT NULL,
    PRIMARY KEY (`client_rule_id`, `acl_role_id`),
    CONSTRAINT `fk.heptacom_admin_open_auth_client_rule_role.client_rule_id`
        FOREIGN KEY (`client_rule_id`)
        REFERENCES `heptacom_admin_open_auth_client_rule` (`id`) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk.heptacom_admin_open_auth_client_rule_role.acl_role_id`
        FOREIGN KEY (`acl_role_id`)
        REFERENCES `acl_role` (`id`) ON UPDATE CASCADE ON DELETE CASCADE
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8mb4
    COLLATE = utf8mb4_unicode_ci;
SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
