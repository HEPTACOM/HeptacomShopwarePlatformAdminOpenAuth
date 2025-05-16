<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1747401001RenameAzureToEntra extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1747401001;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<SQL
            UPDATE `heptacom_admin_open_auth_client`
            SET `provider` = 'microsoft_entra_id_oidc'
            WHERE `provider` = 'microsoft_azure_oidc';
        SQL);

        $connection->executeStatement(<<<SQL
            UPDATE `heptacom_admin_open_auth_client_rule_condition`
            SET `type` = 'heptacomAdminOpenAuthMicrosoftEntraIdOidcGroups'
            WHERE `type` = 'heptacomAdminOpenAuthMicrosoftAzureOidcGroups';
        SQL);
    }
}
