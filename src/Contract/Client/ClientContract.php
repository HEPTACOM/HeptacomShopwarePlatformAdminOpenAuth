<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\Client;

use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\Http\Message\RequestInterface;

abstract class ClientContract
{
    abstract public function getLoginUrl(?string $state, RedirectBehaviour $behaviour): string;

    abstract public function refreshToken(string $refreshToken): TokenPairStruct;

    abstract public function getUser(string $state, string $code, RedirectBehaviour $behaviour): UserStruct;

    abstract public function authorizeRequest(RequestInterface $request, TokenPairStruct $token): RequestInterface;
}
