<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1661561042AddLoginPayload extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1661561042;
    }

    public function update(Connection $connection): void
    {
        $alterTable = <<<'SQL'
ALTER TABLE
    `heptacom_admin_open_auth_login`
ADD COLUMN `payload` JSON NULL AFTER `state`;
SQL;
        $connection->executeStatement($alterTable);

        try {
            $connection->transactional(
                static fn (Connection $connection) => $connection->executeStatement('UPDATE `heptacom_admin_open_auth_login` SET `payload` = \'{}\'')
            );
        } catch (\Throwable $throwable) {
            $this->dropColumnAgain($connection, 1661561042, $throwable);
        }

        try {
            $alterTable = <<<'SQL'
ALTER TABLE
    `heptacom_admin_open_auth_login`
MODIFY COLUMN `payload` JSON NOT NULL;
SQL;
            $connection->executeStatement($alterTable);
        } catch (\Throwable $throwable) {
            $this->dropColumnAgain($connection, 1661561043, $throwable);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }

    private function dropColumnAgain(Connection $connection, int $code, \Throwable $throwable): never
    {
        $connection->executeStatement('ALTER TABLE `heptacom_admin_open_auth_login` DROP COLUMN `payload`');

        throw new \RuntimeException('Migration failed ' . self::class, $code, $throwable);
    }
}
