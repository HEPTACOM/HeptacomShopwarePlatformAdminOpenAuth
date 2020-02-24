<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Database\UserTokenEntity;
use Shopware\Core\Framework\Context;

interface UserTokenInterface
{
    public function setRefreshToken(string $userId, string $clientId, string $refreshToken, Context $context): string;

    public function setAccessToken(string $userId, string $clientId, string $accessToken, Context $context): string;

    public function getToken(string $clientId, string $userId, Context $context): ?UserTokenEntity;
}
