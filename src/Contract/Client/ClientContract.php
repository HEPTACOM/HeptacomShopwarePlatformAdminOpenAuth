<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\Client;

use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\AdminOpenAuth\Contract\TokenPair;
use Heptacom\AdminOpenAuth\Contract\User;
use Psr\Http\Message\RequestInterface;
use Shopware\Core\Framework\Struct\Struct;

abstract class ClientContract extends Struct
{
    abstract public function getLoginUrl(?string $state, RedirectBehaviour $behaviour): string;

    abstract public function getUser(string $state, string $code, RedirectBehaviour $behaviour): User;

    /**
     * @deprecated implement {@see RefreshTokenContract} instead
     */
    public function refreshToken(string $refreshToken): TokenPair
    {
        throw new \BadMethodCallException(\sprintf('"%s" does not support token refresh. Implement "%s" to add support.', static::class, RefreshTokenContract::class));
    }

    /**
     * @deprecated implement {@see RequestAuthorizationContract} instead
     */
    public function authorizeRequest(RequestInterface $request, TokenPair $token): RequestInterface
    {
        throw new \BadMethodCallException(\sprintf('"%s" does not support request authorization. Implement "%s" to add support.', static::class, RequestAuthorizationContract::class));
    }

    public function prepareOAuthRuleScope(OAuthRuleScope $scope): void
    {
        // by default, do nothing
    }
}
