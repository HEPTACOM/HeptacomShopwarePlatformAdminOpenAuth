<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\StateFactory;

use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;

interface LoginStateFactoryInterface
{
    /**
     * @throws LoadClientException
     */
    public function create(string $clientId, ?string $redirectTo, Context $context): string;

    /**
     * @throws LoadClientException
     */
    public function createWithSalesChannel(string $clientId, ?string $redirectTo, SalesChannelEntity $salesChannel, Context $context): string;
}
