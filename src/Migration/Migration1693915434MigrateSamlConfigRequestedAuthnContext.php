<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

final class Migration1693915434MigrateSamlConfigRequestedAuthnContext extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1693915434;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement(<<<'SQL'
UPDATE `heptacom_admin_open_auth_client`
SET `config` = JSON_SET(`config`, '$.requestedAuthnContext', CAST('["urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport"]' as json))
WHERE
    `provider` IN ('saml2', 'jumpcloud')
    AND (
        JSON_EXTRACT(`config`, '$.requestedAuthnContext') IS NULL
        OR JSON_EXTRACT(`config`, '$.requestedAuthnContext') = CAST('[]' as json)
    );
SQL);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
