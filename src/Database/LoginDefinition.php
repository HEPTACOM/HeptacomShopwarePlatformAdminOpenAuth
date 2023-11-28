<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\User\UserDefinition;

final class LoginDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'heptacom_admin_open_auth_login';

    public const DEFAULT_LOGIN_EXPIRY = 600;

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return LoginEntity::class;
    }

    public function getCollectionClass(): string
    {
        return LoginCollection::class;
    }

    public function getDefaults(): array
    {
        return [
            'payload' => [],
            'expiresAt' => \date_create()
                ->setTimestamp(\time() + self::DEFAULT_LOGIN_EXPIRY)
                ->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ];
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            (new IdField('state', 'state'))->addFlags(new Required()),
            new JsonField('payload', 'payload', [], []),
            (new StringField('type', 'type'))->addFlags(new Required()),
            (new DateTimeField('expires_at', 'expiresAt'))->addFlags(new Required()),
            new CreatedAtField(),
            new UpdatedAtField(),

            (new FkField('client_id', 'clientId', ClientDefinition::class))->addFlags(new Required()),
            new FkField('user_id', 'userId', UserDefinition::class),

            new ManyToOneAssociationField('client', 'client_id', ClientDefinition::class, 'id', false),
            new ManyToOneAssociationField('user', 'user_id', UserDefinition::class, 'id', false),
        ]);
    }
}
