<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1702377314MigrateRoleAssignment extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1702377314;
    }

    public function update(Connection $connection): void
    {
        $clients = $connection->executeQuery(<<<'SQL'
            SELECT client.*
            FROM `heptacom_admin_open_auth_client` client
                LEFT JOIN `heptacom_admin_open_auth_client_rule` rule ON client.id = rule.client_id
            WHERE rule.id IS NULL;
SQL)->fetchAllAssociative();

        foreach ($clients as $client) {
            try {
                $connection->beginTransaction();

                $this->migrateClient($client, $connection);

                $connection->commit();
            } catch (\Throwable $e) {
                $connection->rollBack();

                throw $e;
            }
        }
    }

    public function updateDestructive(Connection $connection): void
    {
        $connection->executeStatement(<<<'SQL'
            ALTER TABLE `heptacom_admin_open_auth_client`
                DROP COLUMN `user_become_admin`,
                DROP COLUMN `keep_user_updated`;
SQL);

        $connection->executeStatement(<<<'SQL'
            DROP TABLE IF EXISTS `heptacom_admin_open_auth_client_role`;
SQL);
    }

    private function migrateClient(array $client, Connection $connection): void
    {
        $inserts = [
            'heptacom_admin_open_auth_client_rule' => [],
            'heptacom_admin_open_auth_client_rule_condition' => [],
            'heptacom_admin_open_auth_client_rule_role' => [],
        ];

        // rules

        $ruleId = Uuid::randomBytes();
        $inserts['heptacom_admin_open_auth_client_rule'][] = [
            'id' => $ruleId,
            'client_id' => $client['id'],
            'user_become_admin' => $client['user_become_admin'],
            'position' => 0,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];

        // rule conditions

        $ruleConditionOrContainerId = Uuid::randomBytes();
        $ruleConditionAndContainerId = Uuid::randomBytes();
        $ruleConditionAlwaysValidId = Uuid::randomBytes();

        $inserts['heptacom_admin_open_auth_client_rule_condition'][] = [
            'id' => $ruleConditionOrContainerId,
            'type' => 'orContainer',
            'client_rule_id' => $ruleId,
            'parent_id' => null,
            'value' => '[]',
            'position' => 0,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];
        $inserts['heptacom_admin_open_auth_client_rule_condition'][] = [
            'id' => $ruleConditionAndContainerId,
            'type' => 'andContainer',
            'client_rule_id' => $ruleId,
            'parent_id' => $ruleConditionOrContainerId,
            'value' => '[]',
            'position' => 0,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];
        $inserts['heptacom_admin_open_auth_client_rule_condition'][] = [
            'id' => $ruleConditionAlwaysValidId,
            'type' => 'alwaysValid',
            'client_rule_id' => $ruleId,
            'parent_id' => $ruleConditionAndContainerId,
            'value' => null,
            'position' => 0,
            'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];

        // acl rules

        $aclRoles = $connection->executeQuery(<<<'SQL'
            SELECT *
            FROM `heptacom_admin_open_auth_client_role`
            WHERE client_id = :clientId;
SQL, ['clientId' => $client['id']])->fetchAllAssociative();

        foreach ($aclRoles as $aclRole) {
            $inserts['heptacom_admin_open_auth_client_rule_role'][] = [
                'client_rule_id' => $ruleId,
                'acl_role_id' => $aclRole['acl_role_id'],
            ];
        }

        // insert data

        foreach ($inserts as $table => $rows) {
            foreach ($rows as $row) {
                $connection->insert($table, $row);
            }
        }
    }
}
