<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth;

use Heptacom\AdminOpenAuth\Database\UserTokenEntity;
use Shopware\Core\Framework\Context;

class TokenRefresher
{
    /**
     * @var UserToken
     */
    private $userToken;

    /**
     * @var ClientLoader
     */
    private $clientLoader;

    public function __construct(UserToken $userToken, ClientLoader $clientLoader)
    {
        $this->userToken = $userToken;
        $this->clientLoader = $clientLoader;
    }

    public function refresh(string $clientId, string $userId, Context $context): bool
    {
        $token = $this->userToken->getToken($clientId, $userId, $context);

        if ($token instanceof UserTokenEntity && !empty($token->getRefreshToken())) {
            try {
                $client = $this->clientLoader->load($clientId, $context);
            } catch (Exception\LoadClientException $ignored) {
                return false;
            }

            $accessToken = $client->refreshToken($token->getRefreshToken());

            if (!empty($accessToken)) {
                $this->userToken->setAccessToken($userId, $clientId, $accessToken, $context);
            }
        }

        return false;
    }
}
