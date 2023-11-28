<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1666400732AddConfigurationProperty extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1666400732;
    }

    public function update(Connection $connection): void
    {
        $clients = $connection->executeQuery(<<<SQL
SELECT `id`, `config` FROM `heptacom_admin_open_auth_client`
SQL)->fetchAllAssociative();

        $updateStatement = $connection->prepare(<<<SQL
UPDATE `heptacom_admin_open_auth_client` SET `config` = :config WHERE `id` = :id;
SQL);

        foreach ($clients as $client) {
            $config = json_decode($client['config'] ?? '', true);
            $config['id'] = bin2hex((string) $client['id']);

            $updateStatement->executeQuery([
                'config' => json_encode($config),
                'id' => $client['id'],
            ]);
        }
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
