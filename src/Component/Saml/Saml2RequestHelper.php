<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Saml;

use GuzzleHttp\Exception\RequestException;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Saml2RequestHelper
{
    public static function prepareRequest(RequestInterface $request): RequestInterface
    {
        // nothing special to do here
        return $request;
    }

    /**
     * @throws RequestExceptionInterface
     */
    public static function verifyRequestSuccess(RequestInterface $request, ResponseInterface $response): void
    {
        if ($response->getStatusCode() < 200 || $response->getStatusCode() > 299) {
            throw new RequestException(
                'Request resulted in a non-successful status code: ' . $response->getStatusCode(),
                $request,
                $response
            );
        }

        $contentType = explode(';', $response->getHeaderLine('Content-Type'))[0] ?? '';
        if (!in_array($contentType, ['text/xml', 'application/xml'], true)) {
            throw new RequestException(
                'Expected content type to be of type text/xml or application/xml, received ' . $response->getHeaderLine(
                    'Content-Type'
                ),
                $request,
                $response
            );
        }
    }
}
