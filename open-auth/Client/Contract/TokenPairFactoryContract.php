<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Client\Contract;

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
                ->setTimezone(new \DateTimeZone(\DateTimeZone::UTC));
        }

        return (new TokenPairStruct())
            ->setAccessToken($token->getToken())
            ->setRefreshToken($token->getRefreshToken())
            ->setExpiresAt($expires)
            ->setPassthrough($token->getValues());
    }
}
