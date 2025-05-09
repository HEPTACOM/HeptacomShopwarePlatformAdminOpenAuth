<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route\Support;

use Heptacom\AdminOpenAuth\Contract\User;
use Symfony\Component\HttpFoundation\Request;

readonly class BeforeUserRedirectEvent
{
    public function __construct(
        public string $userId,
        public User $user,
        public string $clientId,
        public ?array $payload
    ) {
    }
}
