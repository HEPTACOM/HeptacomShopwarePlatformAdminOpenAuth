<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\HttpClient\Middleware;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\Client\RefreshTokenContract;
use Heptacom\AdminOpenAuth\Contract\TokenPair;
use Heptacom\AdminOpenAuth\Exception\ClientFeatureNotSupportedException;
use Heptacom\AdminOpenAuth\Exception\TokenExpiredNoRefreshTokenException;
use Heptacom\AdminOpenAuth\Exception\TokenNotFoundException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AuthorizationMiddleware implements HttpClientMiddlewareInterface
{
    public function __construct(
        protected readonly ClientContract $client,
    ) {
    }

    public function process(RequestInterface $request, ClientInterface $handler): ResponseInterface
    {
        $token = $this->getToken();

        if (!$token instanceof TokenPair) {
            throw new TokenNotFoundException(1748643467);
        }

        if ($this->needsRefresh($token)) {
            if ($token->refreshToken === null) {
                throw new TokenExpiredNoRefreshTokenException(1748643469);
            }

            if ($this->client instanceof RefreshTokenContract) {
                $token = $this->client->refreshToken($token->refreshToken);
                $this->storeRefreshedToken($token);
            } else {
                throw new ClientFeatureNotSupportedException($this->client::class, RefreshTokenContract::class, 1748643468);
            }
        }

        $request = $this->client->authorizeRequest($request, $token);

        return $handler->sendRequest($request);
    }

    abstract protected function storeRefreshedToken(TokenPair $token): void;

    abstract protected function getToken(): ?TokenPair;

    protected function needsRefresh(TokenPair $token): bool
    {
        return $token->expiresAt !== null && new \DateTime() > $token->expiresAt;
    }
}
