<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Rule\RuleScope;
use Shopware\Core\System\SalesChannel\SalesChannelContext;

class OAuthRuleScope extends RuleScope
{
    private const CACHE_KEY_NS = '401ebf3b7d32464b95ee7a0bb8d19c88';

    public function __construct(
        private readonly User $user,
        private readonly ClientContract $client,
        private readonly array $clientConfiguration,
        private readonly Context $context,
        private readonly LoggerInterface $logger,
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

    public function getLogger(): LoggerInterface
    {
        return $this->logger;
    }

    public function getCacheKey(): string
    {
        // ignore extensions for the cache key
        $user = clone $this->user;
        foreach ($user->getExtensions() as $name => $extension) {
            $user->removeExtension($name);
        }

        // generate cache key from user and client configuration
        return (string) Uuid::uuid5(self::CACHE_KEY_NS, \json_encode([
            $user,
            $this->clientConfiguration
        ]))->getHex();
    }
}
