<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenAuth;

use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Database\LoginEntity;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use League\OAuth2\Server\RequestEvent;
use Psr\Http\Message\ServerRequestInterface;
use Shopware\Core\Framework\Api\OAuth\User\User;
use Shopware\Core\Framework\Context;

class OneTimeTokenGrant extends PasswordGrant
{
    private LoginInterface $login;

    public function __construct(
        UserRepositoryInterface $userRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository,
        LoginInterface $login
    ) {
        parent::__construct($userRepository, $refreshTokenRepository);
        $this->login = $login;
    }

    public function getIdentifier()
    {
        return 'heptacom_admin_open_auth_one_time_token';
    }

    protected function validateUser(ServerRequestInterface $request, ClientEntityInterface $client)
    {
        $otp = $this->getRequestParameter('one_time_token', $request);

        if (empty($otp)) {
            throw OAuthServerException::invalidRequest('one_time_token');
        }

        $loginState = $this->login->pop($otp, Context::createDefaultContext());

        if (!$loginState instanceof LoginEntity) {
            throw OAuthServerException::invalidRequest('one_time_token', 'Expired');
        }

        $user = new User($loginState->getUserId());

        if (!$user instanceof UserEntityInterface) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::USER_AUTHENTICATION_FAILED, $request));

            throw OAuthServerException::invalidCredentials();
        }

        return $user;
    }
}
