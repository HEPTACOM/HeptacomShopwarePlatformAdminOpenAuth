<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\User\UserEntity;

final class UserEmailEntity extends Entity
{
    use EntityIdTrait;

    public ?string $userId = null;

    public ?string $clientId = null;

    public ?string $email = null;

    public ?ClientEntity $client = null;

    public ?UserEntity $user = null;
}
