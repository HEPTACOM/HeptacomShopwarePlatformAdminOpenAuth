<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\OpenAuth\Struct\TokenPairStruct;
use League\OAuth2\Client\Token\AccessTokenInterface;

class TokenPairFactoryContract
{
    public function fromLeagueToken(AccessTokenInterface $token): TokenPairStruct
    {
        $expires = null;

        if ($token->getExpires() !== null) {
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
}
