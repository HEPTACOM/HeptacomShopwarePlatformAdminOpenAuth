<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route\Support;

use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\AdminOpenAuth\Contract\User;
use Psr\Http\Message\RequestInterface;

class UserRedirectReceivedEvent
{
    public function __construct(
        public readonly User $user,
        public readonly RequestInterface $request,
        public readonly RedirectBehaviour $behaviour
    ) {
    }
}
