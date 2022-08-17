<?php

declare(strict_types = 1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * @todo implement signed/encrypted UserInfo support
 */
class OpenIdConnectService
{

    private const WELL_KNOWN_CACHE_TTL = 900;

    private ClientInterface $oidcHttpClient;

    private LoggerInterface $logger;

    private AdapterInterface $cache;

    private OpenIdConnectTokenVerifier $tokenVerifier;

    private OpenIdConnectConfiguration $config;

    public function __construct(
        ClientInterface $oidcHttpClient,
        LoggerInterface $logger,
        AdapterInterface $cache,
        OpenIdConnectTokenVerifier $tokenVerifier
    ) {
        $this->oidcHttpClient = $oidcHttpClient;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->tokenVerifier = $tokenVerifier;
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
            'response_type' => $this->config->getResponseTypeAuthorizationEndpoint(),
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
            $request = OpenIdConnectRequestHelper::prepareRequest(new Request('GET', $uri), $token);
            $response = $this->oidcHttpClient->sendRequest($request);

            OpenIdConnectRequestHelper::verifyRequestSuccess($request, $response);

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
                    'response_type' => $this->config->getResponseTypeTokenEndpoint(),
                    'client_id' => $this->config->getClientId(),
                    'client_secret' => $this->config->getClientSecret(),
                ], $options),
                '',
                '&',
                PHP_QUERY_RFC3986
            );

            $request = OpenIdConnectRequestHelper::prepareRequest(new Request('POST', $uri, $headers, $body));
            $response = $this->oidcHttpClient->sendRequest($request);

            OpenIdConnectRequestHelper::verifyRequestSuccess($request, $response);

            $json = json_decode((string)$response->getBody(), true);

            $idToken = $json['id_token'] ?? null;
            if ($idToken) {
                if (!$this->tokenVerifier->verify($this->config, $idToken)) {
                    throw new OpenIdConnectException('Verification of id_token failed.');
                }
            }

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

                $request = OpenIdConnectRequestHelper::prepareRequest(new Request('GET', $uri));
                $response = $this->oidcHttpClient->sendRequest($request);

                OpenIdConnectRequestHelper::verifyRequestSuccess($request, $response);

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

    public function getConfig(): OpenIdConnectConfiguration
    {
        return $this->config;
    }

    public function setConfig(OpenIdConnectConfiguration $config): void
    {
        $this->config = $config;
    }
}
