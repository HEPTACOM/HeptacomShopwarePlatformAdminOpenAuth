<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\HttpClient;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\Client\RequestAuthorizationContract;
use Heptacom\AdminOpenAuth\Contract\Client\StandaloneClientContract;
use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\TokenPair;
use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\Exception\ClientFeatureNotSupportedException;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Heptacom\AdminOpenAuth\Service\HttpClient\Middleware\AuthorizationMiddleware;
use Heptacom\AdminOpenAuth\Service\HttpClient\Middleware\ClientAuthorizationMiddleware;
use Heptacom\AdminOpenAuth\Service\HttpClient\Middleware\HttpClientMiddlewareInterface;
use Heptacom\AdminOpenAuth\Service\HttpClient\Middleware\TokenAuthorizationMiddleware;
use Heptacom\AdminOpenAuth\Service\HttpClient\Middleware\UserAuthorizationMiddleware;
use Psr\Http\Client\ClientInterface;
use Shopware\Core\Framework\Context;
use Symfony\Contracts\Cache\CacheInterface;

final class AuthorizedHttpClientFactory
{
    /**
     * @var array<string, AuthorizedHttpClient>
     */
    private array $httpClients = [];

    /**
     * @var HttpClientMiddlewareInterface[]
     */
    private readonly array $middlewares;

    /**
     * @param iterable<HttpClientMiddlewareInterface> $middlewares
     */
    public function __construct(
        private readonly ClientLoaderInterface $clientLoader,
        private readonly UserTokenInterface $userToken,
        private readonly ClientInterface $httpClient,
        private readonly CacheInterface $cache,
        iterable $middlewares,
    ) {
        $this->middlewares = \array_filter(
            \iterator_to_array($middlewares),
            static fn (HttpClientMiddlewareInterface $middleware): bool => !$middleware instanceof AuthorizationMiddleware,
        );
    }

    /**
     * Creates a client dedicated for machine-to-machine communication.
     * The credentials will be fetched from the configured client.
     *
     * @param string[]|null $scopes Scopes to be requested for the client. If null, the configured scopes of the client will be used.
     *
     * @throws LoadClientException|ClientFeatureNotSupportedException
     */
    public function forClient(string $clientId, Context $context, ?array $scopes = null): ClientInterface
    {
        $client = $this->loadClient($clientId, StandaloneClientContract::class, $context);

        $middleware = new ClientAuthorizationMiddleware($client, $clientId, $scopes, $this->cache);

        if ($scopes !== null) {
            \sort($scopes);
        }

        return $this->getHttpClient(
            $this->getHttpClientIdentifier('client', $clientId, ['scopes' => $scopes]),
            $middleware,
        );
    }

    /**
     * Creates a client authenticated for a specific user.
     *
     * @throws LoadClientException|ClientFeatureNotSupportedException
     */
    public function forUser(string $clientId, string $userId, Context $context): ClientInterface
    {
        $client = $this->loadClient($clientId, RequestAuthorizationContract::class, $context);

        $middleware = new UserAuthorizationMiddleware($client, $clientId, $userId, $this->userToken, $context);

        return $this->getHttpClient(
            $this->getHttpClientIdentifier('user', $clientId, ['userId' => $userId]),
            $middleware,
        );
    }

    /**
     * Creates a client authenticated with a specific token.
     *
     * @throws ClientFeatureNotSupportedException
     *
     * @internal
     */
    public function forToken(
        TokenPair $token,
        ClientContract $client,
    ): ClientInterface {
        if (!$client instanceof RequestAuthorizationContract) {
            throw new ClientFeatureNotSupportedException($client::class, RequestAuthorizationContract::class, 1748652077);
        }

        $middleware = new TokenAuthorizationMiddleware($client, $token);

        return $this->getHttpClient(
            $this->getHttpClientIdentifier('token', $client->getApiAlias(), ['token' => $token]),
            $middleware,
        );
    }

    /**
     * @param class-string $feature
     *
     * @throws LoadClientException|ClientFeatureNotSupportedException
     */
    private function loadClient(string $clientId, string $feature, Context $context): ClientContract
    {
        $client = $this->clientLoader->load($clientId, $context);

        if (!$client instanceof $feature) {
            throw new ClientFeatureNotSupportedException($client::class, $feature);
        }

        return $client;
    }

    private function getHttpClient(
        string $httpClientIdentifier,
        HttpClientMiddlewareInterface $authorizationMiddleware,
    ): AuthorizedHttpClient {
        $this->httpClients[$httpClientIdentifier] ??= new AuthorizedHttpClient(
            $this->httpClient,
            [
                ...$this->middlewares,
                $authorizationMiddleware,
            ]
        );

        return $this->httpClients[$httpClientIdentifier];
    }

    private function getHttpClientIdentifier(string $type, string $clientId, array $additionalContext = []): string
    {
        $contextHash = \sha1(\json_encode($additionalContext));

        return \sprintf(
            '%s_%s_%s',
            $type,
            $clientId,
            $contextHash,
        );
    }
}
