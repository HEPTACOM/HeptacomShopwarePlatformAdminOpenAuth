<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Context;

interface UserResolverInterface
{
    public function resolve(User $user, string $state, string $clientId, Context $context): void;
}
