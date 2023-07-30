<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route\Support;

use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\Http\Message\RequestInterface;

class UserRedirectReceivedEvent
{
    public function __construct(
        public readonly UserStruct $user,
        public readonly RequestInterface $request,
        public readonly RedirectBehaviour $behaviour
    ) {
    }
}
