<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route\Support;

use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Database\ClientEntity;

class UserRedirectCompletedEvent
{
    /**
     * @param array<string, mixed>|null $statePayload payload the login state was created with
     * @param string $targetUrl url the user is redirected to, mutable for listeners
     */
    public function __construct(
        public readonly string $userId,
        public readonly User $user,
        public readonly ClientEntity $client,
        public readonly ?array $statePayload,
        public string $targetUrl,
    ) {
    }
}
