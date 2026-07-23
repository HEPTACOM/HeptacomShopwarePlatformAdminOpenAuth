<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\HttpClient\Middleware;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\TokenPair;
use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\Database\UserTokenEntity;
use Shopware\Core\Framework\Context;

final class UserAuthorizationMiddleware extends AuthorizationMiddleware
{
    public function __construct(
        ClientContract $client,
        protected readonly string $clientId,
        protected readonly string $userId,
        protected readonly UserTokenInterface $userToken,
        protected readonly Context $context,
    ) {
        parent::__construct($client);
    }

    protected function getToken(): ?TokenPair
    {
        $userToken = $this->userToken->getToken($this->clientId, $this->userId, $this->context);

        if (!$userToken instanceof UserTokenEntity) {
            return null;
        }

        $token = new TokenPair();
        $token->accessToken = $userToken->accessToken;
        $token->refreshToken = $userToken->refreshToken;
        $token->expiresAt = $userToken->expiresAt;

        return $token;
    }

    protected function storeRefreshedToken(TokenPair $token): void
    {
        $this->userToken->setToken($this->userId, $this->clientId, $token, $this->context);
    }
}
