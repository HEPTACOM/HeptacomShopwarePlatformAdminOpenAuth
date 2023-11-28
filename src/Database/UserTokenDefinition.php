<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\User\UserDefinition;

final class UserTokenDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'heptacom_admin_open_auth_user_token';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return UserTokenEntity::class;
    }

    public function getCollectionClass(): string
    {
        return UserTokenCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new LongTextField('refresh_token', 'refreshToken'))->addFlags(new Required()),
            new LongTextField('access_token', 'accessToken'),
            new DateTimeField('expires_at', 'expiresAt'),
            new CreatedAtField(),
            new UpdatedAtField(),

            (new FkField('client_id', 'clientId', ClientDefinition::class))->addFlags(new Required()),
            new FkField('user_id', 'userId', UserDefinition::class),

            new ManyToOneAssociationField('client', 'client_id', ClientDefinition::class, 'id', false),
            new ManyToOneAssociationField('user', 'user_id', UserDefinition::class, 'id', false),
        ]);
    }
}
