<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\Client;

use Heptacom\AdminOpenAuth\Contract\TokenPair;
use Psr\Http\Message\RequestInterface;

/**
 * If implemented, the client supports authorizing a request based on a token pair.
 */
interface RequestAuthorizationContract
{
    public function authorizeRequest(RequestInterface $request, TokenPair $token): RequestInterface;
}
