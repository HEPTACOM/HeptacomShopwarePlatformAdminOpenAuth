<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Database\UserTokenEntity;
use Heptacom\AdminOpenAuth\Struct\TokenPairStruct;
use Shopware\Core\Framework\Context;

interface UserTokenInterface
{
    public function setToken(string $userId, string $clientId, TokenPairStruct $token, Context $context): string;

    public function getToken(string $clientId, string $userId, Context $context): ?UserTokenEntity;
}