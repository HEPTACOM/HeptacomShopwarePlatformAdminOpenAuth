<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\StateFactory;

use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;

interface ConnectStateFactoryInterface
{
    /**
     * @throws LoadClientException
     */
    public function create(string $clientId, string $userId, string $redirectTo, Context $context): string;
}
