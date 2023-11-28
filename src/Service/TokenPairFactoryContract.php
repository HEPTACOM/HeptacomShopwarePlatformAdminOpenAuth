<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectToken;
use Heptacom\AdminOpenAuth\Contract\TokenPair;
use League\OAuth2\Client\Token\AccessTokenInterface;

class TokenPairFactoryContract
{
    public function fromLeagueToken(AccessTokenInterface $token): TokenPair
    {
        $expires = null;

        if ($token->getExpires() !== null) {
            $expires = \date_create()
                ->setTimestamp($token->getExpires())
                ->setTimezone(new \DateTimeZone('UTC'));
        }

        $result = new TokenPair();
        $result->accessToken = $token->getToken();
        $result->refreshToken = $token->getRefreshToken();
        $result->expiresAt = $expires;
        $result->addArrayExtension('league', $token->getValues());

        return $result;
    }

    public function fromOpenIdConnectToken(OpenIdConnectToken $token): TokenPair
    {
        $expires = null;

        if ($token->getExpiresIn() !== null) {
            $expires = \date_create()
                ->setTimestamp(time() + $token->getExpiresIn())
                ->setTimezone(new \DateTimeZone('UTC'));
        }

        $result = new TokenPair();
        $result->accessToken = $token->getAccessToken();
        $result->refreshToken = $token->getRefreshToken();
        $result->expiresAt = $expires;

        return $result;
    }
}
