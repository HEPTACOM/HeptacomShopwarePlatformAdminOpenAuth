<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\HttpClient\Middleware;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

final readonly class RequestLoggerMiddleware implements HttpClientMiddlewareInterface
{
    /**
     * Set this header to overwrite the logging behavior for the request.
     *
     * As value, you can use any valid log level. Set to "false" to disable logging for the request.
     *
     * The header is removed after processing.
     *
     * @see LogLevel
     */
    public const HEADER_LOGGING_ENABLED = 'X-Request-Logging-Enabled';

    public const DEFAULT_LOG_LEVEL = LogLevel::DEBUG;

    public function __construct(
        protected bool $debugEnabled,
        protected LoggerInterface $logger,
    ) {
    }

    public function process(RequestInterface $request, ClientInterface $handler): ResponseInterface
    {
        $logLevel = $this->getLogLevel($request);

        if ($request->hasHeader(self::HEADER_LOGGING_ENABLED)) {
            $request = $request->withoutHeader(self::HEADER_LOGGING_ENABLED);
        }

        if ($logLevel !== null) {
            $this->logRequest($request, $logLevel);
        }

        $response = $handler->sendRequest($request);

        if ($logLevel !== null) {
            $this->logResponse($response, $logLevel);
        }

        return $response;
    }

    private function getLogLevel(RequestInterface $request): ?string
    {
        $logLevel = $request->getHeader(self::HEADER_LOGGING_ENABLED)[0] ?? self::DEFAULT_LOG_LEVEL;

        if ($logLevel === 'false') {
            return null;
        }

        if ($logLevel === null && $this->debugEnabled) {
            $logLevel = self::DEFAULT_LOG_LEVEL;
        }

        return $logLevel;
    }

    private function logRequest(RequestInterface $request, string $logLevel): void
    {
        $this->logger->log(
            $logLevel,
            'Performing authenticated HTTP request',
            [
                'log_code' => 1748654973,
                'method' => $request->getMethod(),
                'uri' => (string) $request->getUri(),
                'headers' => $request->getHeaders(),
            ]
        );
    }

    private function logResponse(ResponseInterface $response, string $logLevel): void
    {
        $this->logger->log(
            $logLevel,
            \sprintf(
                'Authenticated HTTP request responded with %d %s',
                $response->getStatusCode(),
                $response->getReasonPhrase()
            ),
            [
                'log_code' => 1748655114,
                'status_code' => $response->getStatusCode(),
                'headers' => $response->getHeaders(),
            ]
        );
    }
}
