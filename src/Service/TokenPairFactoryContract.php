<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectToken;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use League\OAuth2\Client\Token\AccessTokenInterface;

class TokenPairFactoryContract
{
    public function fromLeagueToken(AccessTokenInterface $token): TokenPairStruct
    {
        $expires = null;

        if (!\is_null($token->getExpires())) {
            $expires = \date_create()
                ->setTimestamp($token->getExpires())
                ->setTimezone(new \DateTimeZone('UTC'));
        }

        return (new TokenPairStruct())
            ->setAccessToken($token->getToken())
            ->setRefreshToken($token->getRefreshToken())
            ->setExpiresAt($expires)
            ->setPassthrough($token->getValues());
    }

    public function fromOpenIdConnectToken(OpenIdConnectToken $token): TokenPairStruct
    {
        $expires = null;

        if ($token->getExpiresIn() !== null) {
            $expires = \date_create()
                ->setTimestamp(time() + $token->getExpiresIn())
                ->setTimezone(new \DateTimeZone('UTC'));
        }

        return (new TokenPairStruct())
            ->setAccessToken($token->getAccessToken())
            ->setRefreshToken($token->getRefreshToken())
            ->setExpiresAt($expires);
    }
}
