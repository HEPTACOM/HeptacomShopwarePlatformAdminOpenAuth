<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database\Aggregate;

use Heptacom\AdminOpenAuth\Database\ClientRuleDefinition;
use Shopware\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\MappingEntityDefinition;

final class ClientRuleRoleDefinition extends MappingEntityDefinition
{
    public const ENTITY_NAME = 'heptacom_admin_open_auth_client_rule_role';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new FkField('client_rule_id', 'clientRuleId', ClientRuleDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('acl_role_id', 'aclRoleId', AclRoleDefinition::class))->addFlags(new PrimaryKey(), new Required()),
            new ManyToOneAssociationField('clientRule', 'client_rule_id', ClientRuleDefinition::class, 'id'),
            new ManyToOneAssociationField('aclRole', 'acl_role_id', AclRoleDefinition::class, 'id'),
        ]);
    }
}
