<?php

declare(strict_types = 1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @todo implement jwt signature check (see JWKS)
 * @todo implement signed/encrypted UserInfo support
 */
class OpenIdConnectService
{
    private const WELL_KNOWN_CACHE_TTL = 900;

    private ClientInterface $httpClient;

    private AdapterInterface $cache;

    private OpenIdConnectConfiguration $config;

    public function __construct(ClientInterface $oidcHttpClient, AdapterInterface $cache)
    {
        $this->httpClient = $oidcHttpClient;
        $this->cache = $cache;
        $this->config = new OpenIdConnectConfiguration();
    }

    public function createWithConfig(OpenIdConnectConfiguration $config): self
    {
        $service = new self($this->httpClient, $this->cache);
        $service->setConfig($config);

        return $service;
    }

    public function getAuthorizationUrl(array $params = []): string
    {
        $defaultParams = [
            'scope' => implode(',', $this->config->getRequestedScopes()),
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

            $json = json_decode((string)$response->getBody(), true);
            $user = new OpenIdConnectUser();
            $user->assign($json);

            return $user;
        } catch (ClientExceptionInterface $e) {
            // @todo handle
            throw new OpenIdConnectException('Could not retrieve user info.');
        }
    }

    public function getAccessToken(string $grantType, array $options = []): OpenIdConnectToken
    {
        $supportedGrantTypes = $this->config->getGrantTypesSupported();
        if ($supportedGrantTypes !== null && !in_array($grantType, $supportedGrantTypes)) {
            throw new OpenIdConnectException(
                sprintf('%s is a not supported grant type for this identity provider', $grantType)
            );
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

            $json = json_decode((string)$response->getBody(), true);
            $tokenResponse = new OpenIdConnectToken();
            $tokenResponse->assign($json);

            return $tokenResponse;
        } catch (ClientExceptionInterface $e) {
            // @todo handle
            throw new OpenIdConnectException('Could not retrieve access token.');
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

        $cache_key = sprintf(
            'heptacom-admin-open-auth_well-known_%s',
            md5($openIdConnectDiscoveryDocument),
        );
        $cachedWellKnown = $this->cache->getItem($cache_key);

        if (!$cachedWellKnown->isHit()) {
            try {
                $uri = new Uri($openIdConnectDiscoveryDocument);

                $request = $this->prepareRequest(new Request('GET', $uri));
                $response = $this->httpClient->sendRequest($request);

                if ($response->getHeaderLine('Content-Type') !== 'application/json') {
                    throw new OpenIdConnectException('Could not load openid-configuration from issuer '.$uri);
                }

                $cachedWellKnown->set(json_decode((string)$response->getBody(), true));
                $cachedWellKnown->expiresAfter(self::WELL_KNOWN_CACHE_TTL);
                $this->cache->save($cachedWellKnown);
            } catch (ClientExceptionInterface $e) {
                // @todo log
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

    public function getConfig(): OpenIdConnectConfiguration
    {
        return $this->config;
    }

    public function setConfig(OpenIdConnectConfiguration $config): void
    {
        $this->config = $config;
    }
}
