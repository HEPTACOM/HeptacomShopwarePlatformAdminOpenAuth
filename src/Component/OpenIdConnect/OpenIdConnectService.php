<?php

declare(strict_types = 1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;

class OpenIdConnectService
{

    private ClientInterface $httpClient;

    private OpenIdConnectConfiguration $config;

    public function __construct(ClientInterface $httpClient, OpenIdConnectConfiguration $config)
    {
        $this->httpClient = $httpClient;
        $this->config = $config;
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

            $request = $this->prepareRequest(
                new Request(
                    'POST',
                    $uri,
                    [
                        'Content-Type' => 'application/x-www-form-urlencoded'
                    ],
                    http_build_query(
                        array_merge([
                            'grant_type' => $grantType,
                            'client_id' => $this->config->getClientId(),
                            'client_secret' => $this->config->getClientSecret(),
                        ], $options),
                        '',
                        '&',
                        PHP_QUERY_RFC3986
                    )
                )
            );
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

        $issuer = $this->config->getIssuer();
        if (!$issuer) {
            return false;
        }

        try {
            $uri = new Uri($this->config->getIssuer().'/.well-known/openid-configuration');
            $request = $this->prepareRequest(new Request('GET', $uri));
            $response = $this->httpClient->sendRequest($request);

            if (
                $response->getStatusCode() < 200 || $response->getStatusCode() >= 300 ||
                $response->getHeaderLine('Content-Type') !== 'application/json'
            ) {
                throw new OpenIdConnectException('Could not load openid-configuration from issuer '.$uri);
            }

            $json = json_decode((string)$response->getBody(), true);

            $this->config->assign($json);
            $this->config->setWellKnownDiscovered(true);
        } catch (ClientExceptionInterface $e) {
            // @todo log
            return false;
        }

        // @todo cache

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
