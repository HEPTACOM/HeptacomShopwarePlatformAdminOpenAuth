<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\OpenAuth\Struct\TokenPairStruct;
use League\OAuth2\Client\Token\AccessTokenInterface;

interface TokenPairFactoryInterface
{
    public function fromLeagueToken(AccessTokenInterface $token): TokenPairStruct;
}
