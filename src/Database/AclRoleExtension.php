<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Heptacom\AdminOpenAuth\Database\Aggregate\ClientRuleRoleDefinition;
use Shopware\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

final class AclRoleExtension extends EntityExtension
{
    public function getDefinitionClass(): string
    {
        return AclRoleDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        parent::extendFields($collection);

        $collection->add(
            (new ManyToManyAssociationField('heptacomOpenAuthClientRules', ClientRuleDefinition::class, ClientRuleRoleDefinition::class, 'acl_role_id', 'client_rule_id'))->addFlags(new CascadeDelete()),
        );
    }
}
