<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\StateFactory;

use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;

interface LoginStateFactoryInterface
{
    /**
     * @throws LoadClientException
     */
    public function create(string $clientId, string $redirectTo, Context $context): string;
}
