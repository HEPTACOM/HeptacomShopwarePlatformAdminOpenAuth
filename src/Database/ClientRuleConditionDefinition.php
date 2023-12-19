<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Content\Rule\RuleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ChildrenAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ParentAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ParentFkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ClientRuleConditionDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'heptacom_admin_open_auth_client_rule_condition';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ClientRuleConditionEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ClientRuleConditionCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('type', 'type'))->addFlags(new Required()),
            (new FkField('client_rule_id', 'clientRuleId', ClientRuleDefinition::class))->addFlags(new Required()),
            new ParentFkField(self::class),
            new JsonField('value', 'value'),
            new IntField('position', 'position'),
            new ManyToOneAssociationField('clientRule', 'client_rule_id', RuleDefinition::class, 'id', false),
            new ParentAssociationField(self::class, 'id'),
            new ChildrenAssociationField(self::class),
        ]);
    }
}
