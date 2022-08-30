<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Heptacom\AdminOpenAuth\Database\Aggregate\ClientAclRoleDefinition;
use Shopware\Core\Framework\Api\Acl\Role\AclRoleDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ClientDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'heptacom_admin_open_auth_client';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ClientEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ClientCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new StringField('name', 'name'))->addFlags(new Required()),
            (new StringField('provider', 'provider'))->addFlags(new Required()),
            (new BoolField('active', 'active'))->addFlags(new Required()),
            (new BoolField('login', 'login'))->addFlags(new Required()),
            (new BoolField('connect', 'connect'))->addFlags(new Required()),
            (new BoolField('store_user_token', 'storeUserToken'))->addFlags(new Required()),
            new BoolField('user_become_admin', 'userBecomeAdmin'),
            (new JsonField('config', 'config', [], []))->addFlags(new Required()),
            new CreatedAtField(),
            new UpdatedAtField(),

            new OneToManyAssociationField('logins', LoginDefinition::class, 'client_id', 'id'),
            new OneToManyAssociationField('userEmails', UserEmailDefinition::class, 'client_id', 'id'),
            new OneToManyAssociationField('userKeys', UserKeyDefinition::class, 'client_id', 'id'),
            new OneToManyAssociationField('userTokens', UserTokenDefinition::class, 'client_id', 'id'),

            new ManyToManyAssociationField('defaultAclRoles', AclRoleDefinition::class, ClientAclRoleDefinition::class, 'client_id', 'acl_role_id'),
        ]);
    }
}
