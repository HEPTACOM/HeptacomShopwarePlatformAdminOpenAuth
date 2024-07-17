<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;
use Shopware\Core\Framework\Uuid\Uuid;

class Migration1720193260MigrateToRuleActions extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1720193260;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<SQL
            ALTER TABLE `heptacom_admin_open_auth_client_rule`
                ADD COLUMN `action_name` VARCHAR(64) NOT NULL AFTER `client_id`,
                ADD COLUMN `action_config` JSON NULL AFTER `action_name`,
                MODIFY COLUMN `user_become_admin` BOOLEAN DEFAULT FALSE NOT NULL,
                ADD CONSTRAINT `json.heptacom_admin_open_auth_client_rule.action_config` CHECK (JSON_VALID (`action_config`))
        SQL);

        $this->migrateRoleAssignment($connection);

        $connection->executeStatement(<<<SQL
            ALTER TABLE `heptacom_admin_open_auth_client_rule`
                MODIFY COLUMN `action_config` JSON NOT NULL
        SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
        $connection->executeStatement(<<<SQL
            ALTER TABLE `heptacom_admin_open_auth_client_rule`
                DROP COLUMN `user_become_admin`
        SQL);

        $connection->executeStatement(<<<SQL
            DROP TABLE `heptacom_admin_open_auth_client_rule_role`
        SQL);
    }

    private function migrateRoleAssignment(Connection $connection)
    {
        $clientRules = $connection->executeQuery(<<<SQL
            SELECT `id`, `user_become_admin` FROM `heptacom_admin_open_auth_client_rule`
        SQL);

        while ($clientRule = $clientRules->fetchAssociative()) {
            $aclRoleIds = $connection->fetchFirstColumn(
                <<<SQL
                    SELECT `acl_role_id`
                    FROM `heptacom_admin_open_auth_client_rule_role`
                    WHERE `client_rule_id` = :clientRuleId
                SQL,
                ['clientRuleId' => $clientRule['id']]
            );

            $actionConfig = [
                'userBecomeAdmin' => (bool) $clientRule['user_become_admin'],
                'aclRoleIds' => Uuid::fromBytesToHexList($aclRoleIds),
            ];

            $connection->executeStatement(
                <<<SQL
                    UPDATE `heptacom_admin_open_auth_client_rule`
                    SET
                        `action_name` = 'heptacomAdminOpenAuthRoleAssignment',
                        `action_config` = :actionConfig
                    WHERE `id` = :clientRuleId
                SQL,
                [
                    'clientRuleId' => $clientRule['id'],
                    'actionConfig' => \json_encode($actionConfig),
                ]
            );
        }
    }
}
