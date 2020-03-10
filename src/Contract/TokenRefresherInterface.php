<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Struct\TokenPairStruct;
use Shopware\Core\Framework\Context;

interface TokenRefresherInterface
{
    public function refresh(string $clientId, string $userId, int $secondsValid, Context $context): ?TokenPairStruct;
}
