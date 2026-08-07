<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Support;

use Heptacom\AdminOpenAuth\Contract\User;
use Shopware\Core\Framework\Context;

readonly class UserUpdatedEvent
{
    /**
     * @param array<string, mixed>|null $statePayload payload the login state was created with
     */
    public function __construct(
        public User $user,
        public string $userId,
        public bool $isNew,
        public string $clientId,
        public ?array $statePayload,
        public Context $context,
    ) {
    }
}
