<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Context;

interface TokenRefresherInterface
{
    public function refresh(string $clientId, string $userId, Context $context): bool;
}
