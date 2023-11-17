<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\StateFactory;

use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;

interface ConfirmStateFactoryInterface
{
    /**
     * @throws LoadClientException
     */
    public function create(string $clientId, string $userId, Context $context): string;
}
