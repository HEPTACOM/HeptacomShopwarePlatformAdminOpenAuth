<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\HttpClient;

use Heptacom\AdminOpenAuth\Service\HttpClient\Middleware\HttpClientMiddlewareInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

final readonly class AuthorizedHttpClient implements ClientInterface
{
    /**
     * @param HttpClientMiddlewareInterface[] $middlewares
     */
    public function __construct(
        private ClientInterface $httpClient,
        private array $middlewares,
    ) {
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->next($request, ...$this->middlewares);
    }

    private function next(RequestInterface $request, HttpClientMiddlewareInterface ...$middlewares): ResponseInterface
    {
        $middleware = \array_shift($middlewares);

        if ($middleware instanceof HttpClientMiddlewareInterface) {
            $next = (function (RequestInterface $request) use ($middlewares) {
                return $this->next($request, ...$middlewares);
            })(...);

            $handler = new class($next) implements ClientInterface {
                private \Closure $next;

                public function __construct(\Closure $next)
                {
                    $this->next = $next;
                }

                public function sendRequest(RequestInterface $request): ResponseInterface
                {
                    return ($this->next)($request);
                }
            };

            return $middleware->process($request, $handler);
        }

        return $this->httpClient->sendRequest($request);
    }
}
