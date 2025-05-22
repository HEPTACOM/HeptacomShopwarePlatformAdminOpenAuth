<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenAuth;

use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Database\LoginEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Shopware\Core\Framework\Api\OAuth\User\User;
use Shopware\Core\Framework\Context;

final class OneTimeTokenGrant extends PasswordGrant
{
    public function __construct(
        UserRepositoryInterface $userRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository,
        private readonly LoginInterface $login
    ) {
        parent::__construct($userRepository, $refreshTokenRepository);
    }

    #[\Override]
    public function getIdentifier(): string
    {
        return 'heptacom_admin_open_auth_one_time_token';
    }

    #[\Override]
    protected function validateUser(ServerRequestInterface $request, ClientEntityInterface $client): UserEntityInterface
    {
        $otp = $this->getRequestParameter('one_time_token', $request);

        if (empty($otp)) {
            throw OAuthServerException::invalidRequest('one_time_token');
        }

        $loginState = $this->login->pop($otp, Context::createDefaultContext());

        if (!$loginState instanceof LoginEntity || $loginState->type !== 'login') {
            throw OAuthServerException::invalidRequest('one_time_token', 'Expired');
        }

        return new User($loginState->userId);
    }
}
