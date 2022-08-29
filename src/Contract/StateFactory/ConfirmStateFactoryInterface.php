<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\StateFactory;

use Shopware\Core\Framework\Context;

interface ConfirmStateFactoryInterface
{
    public function create(string $clientId, string $userId, Context $context): string;
}
