<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\HttpClient\Middleware;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @internal Please note that this interface can be used for extending the plugin. However, do not consider it as public API. It might change in future versions without notice.
 */
interface HttpClientMiddlewareInterface
{
    /**
     * When an outbound HTTP request is performed using the @see \Psr\Http\Client\ClientInterface,
     * a chain containing every implementation of this interface is executed.
     *
     * - Implementations MAY modify the request before passing it to the handler.
     * - Implementations SHOULD pass the request to the handler, to let the chain execution continue.
     * - Implementations MAY modify the handler's return value before returning it.
     *
     * A possible implementation could look like this:
     *
     * ```
     *      // modify the request
     *      $request = $request->withHeader('User-Agent', 'Middleware example');
     *
     *      // pass the request to the handler
     *      $response = $handler->sendRequest($request);
     *
     *      // modify the response
     *      $response = $response->withHeader('Server', 'Middleware example');
     *
     *      // return the response
     *      return $response;
     * ```
     */
    public function process(RequestInterface $request, ClientInterface $handler): ResponseInterface;
}
