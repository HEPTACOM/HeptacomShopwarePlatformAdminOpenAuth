<?php

declare(strict_types = 1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @todo implement jwt signature check (see JWKS)
 * @todo implement signed/encrypted UserInfo support
 */
class OpenIdConnectService
{

    private const WELL_KNOWN_CACHE_TTL = 900;

    private ClientInterface $httpClient;

    private LoggerInterface $logger;

    private AdapterInterface $cache;

    private OpenIdConnectConfiguration $config;

    public function __construct(
        ClientInterface $oidcHttpClient,
        LoggerInterface $logger,
        AdapterInterface $cache
    ) {
        $this->httpClient = $oidcHttpClient;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->config = new OpenIdConnectConfiguration();
    }

    public function createWithConfig(OpenIdConnectConfiguration $config): self
    {
        $service = clone $this;
        $service->setConfig($config);

        return $service;
    }

    public function getAuthorizationUrl(array $params = []): string
    {
        $defaultParams = [
            'scope' => implode(' ', $this->config->getScopes()),
            'response_type' => $this->config->getResponseType(),
            'client_id' => $this->config->getClientId(),
            'redirect_uri' => $this->config->getRedirectUri(),
        ];
        $queryParams = array_merge($defaultParams, $params);

        return $this->getConfig()->getAuthorizationEndpoint().'?'.http_build_query(
                $queryParams,
                '',
                '&',
                PHP_QUERY_RFC3986
            );
    }

    public function getUserInfo(OpenIdConnectToken $token): OpenIdConnectUser
    {
        try {
            $uri = new Uri((string)$this->config->getUserinfoEndpoint());
            $request = $this->prepareRequest(new Request('GET', $uri), $token);
            $response = $this->httpClient->sendRequest($request);

            $this->verifyRequestSuccess($request, $response);

            $json = json_decode((string)$response->getBody(), true);
            $user = new OpenIdConnectUser();
            $user->assign($json);

            return $user;
        } catch (ClientExceptionInterface $e) {
            $message = sprintf('Could not retrieve user info: %s', $e->getMessage());
            $this->logger->error($message, $e->getTrace());
            throw new OpenIdConnectException($message);
        }
    }

    public function getAccessToken(string $grantType, array $options = []): OpenIdConnectToken
    {
        $supportedGrantTypes = $this->config->getGrantTypesSupported();
        if ($supportedGrantTypes !== null && !in_array($grantType, $supportedGrantTypes)) {
            $message = sprintf('%s is a not supported grant type for this identity provider', $grantType);
            $this->logger->critical($message);
            throw new OpenIdConnectException($message);
        }

        try {
            $uri = new Uri((string)$this->config->getTokenEndpoint());
            $headers = [
                'Content-Type' => 'application/x-www-form-urlencoded',
            ];
            $body = http_build_query(
                array_merge([
                    'grant_type' => $grantType,
                    'client_id' => $this->config->getClientId(),
                    'client_secret' => $this->config->getClientSecret(),
                ], $options),
                '',
                '&',
                PHP_QUERY_RFC3986
            );

            $request = $this->prepareRequest(new Request('POST', $uri, $headers, $body));
            $response = $this->httpClient->sendRequest($request);

            $this->verifyRequestSuccess($request, $response);

            $json = json_decode((string)$response->getBody(), true);
            $tokenResponse = new OpenIdConnectToken();
            $tokenResponse->assign($json);

            return $tokenResponse;
        } catch (ClientExceptionInterface $e) {
            $message = sprintf('Could not retrieve access token: %s', $e->getMessage());
            $this->logger->error($message, $e->getTrace());
            throw new OpenIdConnectException($message);
        }
    }

    public function discoverWellKnown(): bool
    {
        if ($this->config->isWellKnownDiscovered()) {
            return true;
        }

        $openIdConnectDiscoveryDocument = $this->config->getDiscoveryDocumentUrl();
        if (empty($openIdConnectDiscoveryDocument)) {
            $issuer = $this->config->getIssuer();

            if (empty($issuer)) {
                return false;
            } else {
                $openIdConnectDiscoveryDocument = $this->config->getIssuer().'/.well-known/openid-configuration';
            }
        }
        $cacheKey = sprintf(
            'heptacom-admin-open-auth_well-known_%s',
            md5($openIdConnectDiscoveryDocument),
        );
        $cachedWellKnown = $this->cache->getItem($cacheKey);

        if (!$cachedWellKnown->isHit()) {
            try {
                $uri = new Uri($openIdConnectDiscoveryDocument);

                $request = $this->prepareRequest(new Request('GET', $uri));
                $response = $this->httpClient->sendRequest($request);

                $this->verifyRequestSuccess($request, $response);

                $cachedWellKnown->set(json_decode((string)$response->getBody(), true));
                $cachedWellKnown->expiresAfter(self::WELL_KNOWN_CACHE_TTL);
                $this->cache->save($cachedWellKnown);
            } catch (ClientExceptionInterface $e) {
                $message = sprintf(
                    'Could not discover OpenID Connect metadata from %s. %s',
                    $openIdConnectDiscoveryDocument,
                    $e->getMessage()
                );
                $this->logger->warning($message, $e->getTrace());

                return false;
            }
        }

        $this->config->assign($cachedWellKnown->get());
        $this->config->setWellKnownDiscovered(true);

        return true;
    }

    protected function prepareRequest(Request $request, ?OpenIdConnectToken $token = null): Request
    {
        if ($token !== null) {
            $request = $request->withAddedHeader('Authorization', $token->getTokenType().' '.$token->getAccessToken());
        }

        return $request;
    }

    public function verifyRequestSuccess(RequestInterface $request, ResponseInterface $response): void
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

    public function getConfig(): OpenIdConnectConfiguration
    {
        return $this->config;
    }

    public function setConfig(OpenIdConnectConfiguration $config): void
    {
        $this->config = $config;
    }
}
