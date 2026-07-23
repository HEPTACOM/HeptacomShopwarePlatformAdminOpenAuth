<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\HttpClient\Middleware;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\Client\StandaloneClientContract;
use Heptacom\AdminOpenAuth\Contract\TokenPair;
use Heptacom\AdminOpenAuth\Exception\ClientFeatureNotSupportedException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class ClientAuthorizationMiddleware extends AuthorizationMiddleware
{
    private const CACHE_KEY = 'heptacom_admin_open_auth_client_token_%s';

    public function __construct(
        ClientContract $client,
        private readonly string $clientId,
        private readonly ?array $clientScopes = null,
        private readonly CacheInterface $cache,
    ) {
        parent::__construct($client);
    }

    protected function getToken(): ?TokenPair
    {
        if (!$this->client instanceof StandaloneClientContract) {
            throw new ClientFeatureNotSupportedException($this->client::class, StandaloneClientContract::class, 1748642863);
        }

        $cacheKey = $this->buildCacheKey();

        return $this->cache->get($cacheKey, fn (ItemInterface $cacheItem) => $this->cacheToken($cacheItem));
    }

    protected function storeRefreshedToken(TokenPair $token): void
    {
        $cacheKey = $this->buildCacheKey();
        $this->cache->delete($cacheKey);

        $this->cache->get($cacheKey, fn (ItemInterface $cacheItem) => $this->cacheToken($cacheItem, $token));
    }

    private function buildCacheKey(): string
    {
        return \sprintf(self::CACHE_KEY, $this->clientId);
    }

    private function cacheToken(ItemInterface $cacheItem, ?TokenPair $token = null): TokenPair
    {
        if ($token === null) {
            $token = $this->client->getClientToken($this->clientScopes);
        }

        if ($token->expiresAt !== null) {
            $cacheItem->expiresAt($token->expiresAt);
        }

        return $token;
    }
}
