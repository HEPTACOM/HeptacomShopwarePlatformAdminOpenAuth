<?php

declare(strict_types = 1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class OpenIdConnectRequestHelper
{
    public static function prepareRequest(RequestInterface $request, ?OpenIdConnectToken $token = null): RequestInterface
    {
        if ($token !== null) {
            $request = $request->withAddedHeader('Authorization', $token->getTokenType().' '.$token->getAccessToken());
        }

        return $request;
    }

    /**
     * @throws RequestExceptionInterface
     */
    public static function verifyRequestSuccess(RequestInterface $request, ResponseInterface $response): void
    {
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new RequestException(
                'Request resulted in a non-successful status code: '.$response->getStatusCode(),
                $request,
                $response
            );
        }

        if (substr($response->getHeaderLine('Content-Type'), 0, 16) !== 'application/json') {
            throw new RequestException(
                'Expected content type to be of type application/json, received '.$response->getHeaderLine(
                    'Content-Type'
                ), $request, $response
            );
        }
    }
}
