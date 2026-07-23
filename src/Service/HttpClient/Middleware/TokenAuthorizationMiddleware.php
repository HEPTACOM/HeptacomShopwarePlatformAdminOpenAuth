<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\HttpClient\Middleware;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\TokenPair;

final class TokenAuthorizationMiddleware extends AuthorizationMiddleware
{
    public function __construct(
        ClientContract $client,
        protected TokenPair $tokenPair,
    ) {
        parent::__construct($client);
    }

    protected function getToken(): ?TokenPair
    {
        return $this->tokenPair;
    }

    protected function storeRefreshedToken(TokenPair $token): void
    {
        $this->tokenPair = $token;
    }
}
