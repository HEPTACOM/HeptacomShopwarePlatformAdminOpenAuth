<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1661525744ForeignKeyCascade extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1661525744;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<SQL
ALTER TABLE `heptacom_admin_open_auth_login`
DROP FOREIGN KEY `fk.heptacom_admin_open_auth_login.user_id`,
DROP INDEX `fk.heptacom_admin_open_auth_login.user_id`
SQL);
        $connection->executeStatement(<<<SQL
ALTER TABLE `heptacom_admin_open_auth_login`
ADD CONSTRAINT `fk.heptacom_admin_open_auth_login.user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
SQL);

        $connection->executeStatement(<<<SQL
ALTER TABLE `heptacom_admin_open_auth_user_email`
DROP FOREIGN KEY `fk.heptacom_admin_open_auth_user_email.user_id`,
DROP INDEX `fk.heptacom_admin_open_auth_user_email.user_id`
SQL);
        $connection->executeStatement(<<<SQL
ALTER TABLE `heptacom_admin_open_auth_user_email`
ADD CONSTRAINT `fk.heptacom_admin_open_auth_user_email.user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
SQL);

        $connection->executeStatement(<<<SQL
ALTER TABLE `heptacom_admin_open_auth_user_key`
DROP FOREIGN KEY `fk.heptacom_admin_open_auth_user_key.user_id`,
DROP INDEX `fk.heptacom_admin_open_auth_user_key.user_id`
SQL);
        $connection->executeStatement(<<<SQL
ALTER TABLE `heptacom_admin_open_auth_user_key`
ADD CONSTRAINT `fk.heptacom_admin_open_auth_user_key.user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
SQL);

        $connection->executeStatement(<<<SQL
ALTER TABLE `heptacom_admin_open_auth_user_token`
DROP FOREIGN KEY `fk.heptacom_admin_open_auth_user_token.user_id`,
DROP INDEX `fk.heptacom_admin_open_auth_user_token.user_id`
SQL);
        $connection->executeStatement(<<<SQL
ALTER TABLE `heptacom_admin_open_auth_user_token`
ADD CONSTRAINT `fk.heptacom_admin_open_auth_user_token.user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
