<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\System\User\UserEntity;

final class LoginEntity extends Entity
{
    use EntityIdTrait;

    public ?string $clientId = null;

    public ?string $state = null;

    public array $payload = [];

    public string $type = '';

    public \DateTime $expiresAt;

    public ?string $userId = null;

    public ?ClientEntity $client = null;

    public ?UserEntity $user = null;
}
