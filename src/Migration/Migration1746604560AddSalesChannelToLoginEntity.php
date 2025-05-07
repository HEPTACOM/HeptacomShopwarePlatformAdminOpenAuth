<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1746604560AddSalesChannelToLoginEntity extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1702921670;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(
            <<<'SQL'
                ALTER TABLE `heptacom_admin_open_auth_login`
                    ADD COLUMN `sales_channel_id` BINARY(16) NULL AFTER `user_id`;
                ALTER TABLE `heptacom_admin_open_auth_login`
                    ADD CONSTRAINT `fk.heptacom_admin_open_auth_login.sales_channel_id`
                        FOREIGN KEY (`sales_channel_id`) REFERENCES `sales_channel` (`id`)
                            ON DELETE CASCADE
                            ON UPDATE CASCADE
            SQL
        );
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
