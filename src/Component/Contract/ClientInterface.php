<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Contract;

use Heptacom\AdminOpenAuth\Struct\TokenPairStruct;
use Heptacom\AdminOpenAuth\Struct\UserStruct;
use League\OAuth2\Client\Provider\AbstractProvider;

interface ClientInterface
{
    public function getLoginUrl(string $state): string;

    public function getUser(string $state, string $code): UserStruct;

    public function refreshToken(string $refreshToken): TokenPairStruct;

    public function getInnerClient(): AbstractProvider;
}