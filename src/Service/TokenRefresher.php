<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\TokenRefresherInterface;
use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\Database\UserTokenEntity;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Shopware\Core\Framework\Context;

final class TokenRefresher implements TokenRefresherInterface
{
    public function __construct(private readonly UserTokenInterface $userToken, private readonly ClientLoaderInterface $clientLoader, private readonly ClientFeatureCheckerInterface $clientFeatureChecker)
    {
    }

    public function refresh(string $clientId, string $userId, int $secondsValid, Context $context): ?TokenPairStruct
    {
        if (!$this->clientFeatureChecker->canStoreUserTokens($clientId, $context)) {
            return null;
        }

        $token = $this->userToken->getToken($clientId, $userId, $context);

        if ($token instanceof UserTokenEntity && !empty($token->getRefreshToken())) {
            if ($token->getExpiresAt() !== null) {
                $now = \date_create();
                $expirationDelta = $token->getExpiresAt()->getTimestamp() - $now->getTimestamp();

                if ($expirationDelta > $secondsValid && $expirationDelta > 0) {
                    return (new TokenPairStruct())
                        ->setAccessToken($token->getAccessToken())
                        ->setExpiresAt($token->getExpiresAt())
                        ->setRefreshToken($token->getRefreshToken());
                }
            }

            try {
                $client = $this->clientLoader->load($clientId, $context);
            } catch (LoadClientException $ignored) {
                return null;
            }

            $tokenPair = $client->refreshToken($token->getRefreshToken());
            $this->userToken->setToken($userId, $clientId, $tokenPair, $context);

            return $tokenPair;
        }

        return null;
    }
}
