<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route\Support;

use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Database\ClientEntity;

readonly class UserRedirectAuthenticationEvent
{
    public function __construct(
        public User $user,
        public ClientEntity $client,
    ) {
    }
}
