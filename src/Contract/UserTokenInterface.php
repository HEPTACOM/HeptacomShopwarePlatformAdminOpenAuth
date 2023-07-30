<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Database\UserTokenEntity;
use Shopware\Core\Framework\Context;

interface UserTokenInterface
{
    public function setToken(string $userId, string $clientId, TokenPair $token, Context $context): string;

    public function getToken(string $clientId, string $userId, Context $context): ?UserTokenEntity;
}
