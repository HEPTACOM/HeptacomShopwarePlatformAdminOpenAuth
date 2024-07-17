<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\CascadeDelete;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ClientRuleDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'heptacom_admin_open_auth_client_rule';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ClientRuleEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ClientRuleCollection::class;
    }

    public function getDefaults(): array
    {
        return [
            'stopOnMatch' => false,
        ];
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new FkField('client_id', 'clientId', ClientDefinition::class))->addFlags(new Required()),
            (new StringField('action_name', 'actionName'))->addFlags(new Required()),
            (new JsonField('action_config', 'actionConfig'))->addFlags(new Required()),
            (new BoolField('stop_on_match', 'stopOnMatch'))->addFlags(new Required()),
            new IntField('position', 'position'),
            new CreatedAtField(),
            new UpdatedAtField(),

            new ManyToOneAssociationField('client', 'client_id', ClientDefinition::class, 'id', false),
            (new OneToManyAssociationField(
                'conditions',
                ClientRuleConditionDefinition::class,
                'client_rule_id',
                'id'
            ))->addFlags(new CascadeDelete()),
        ]);
    }
}
