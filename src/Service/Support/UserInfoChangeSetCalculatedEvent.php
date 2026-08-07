<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Support;

use Heptacom\AdminOpenAuth\Contract\User;
use Shopware\Core\Framework\Context;

class UserInfoChangeSetCalculatedEvent
{
    /**
     * @param array<string, mixed> $changeSet columns of `user` to write, mutable for listeners
     */
    public function __construct(
        public readonly User $user,
        public readonly bool $isNew,
        public readonly string $clientId,
        public readonly Context $context,
        public array $changeSet,
    ) {
    }
}
