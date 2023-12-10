<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Rule\RuleScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class OAuthRuleScope extends RuleScope
{
    public function __construct(
        private readonly User $user,
        private readonly ClientContract $client,
        private readonly array $clientConfiguration,
        private readonly Context $context,
    ) {
    }

    public function getContext(): Context
    {
        return $this->context;
    }

    /**
     * This method is not available in the OAuth scope!
     *
     * @throws \RuntimeException
     */
    public function getSalesChannelContext(): SalesChannelContext
    {
        throw new \RuntimeException('Sales channel context not available for rules in OAuth scope!');
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getClient(): ClientContract
    {
        return $this->client;
    }

    public function getClientConfiguration(): array
    {
        return $this->clientConfiguration;
    }
}
