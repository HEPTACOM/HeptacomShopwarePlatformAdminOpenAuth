<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Saml;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\AuthnRequest;
use OneLogin\Saml2\Error;
use OneLogin\Saml2\Settings;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class Saml2ServiceProviderService
{
    // TODO: SAML: implement
    private const METADATA_CACHE_TTL = 900;

    public const ID_PREFIX = 'saml2_provider_';

    private ClientInterface $samlHttpClient;

    private LoggerInterface $logger;

    private AdapterInterface $cache;

    private Saml2ServiceProviderConfiguration $config;

    public function __construct(ClientInterface $samlHttpClient, LoggerInterface $logger, AdapterInterface $cache)
    {
        $this->samlHttpClient = $samlHttpClient;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->config = new Saml2ServiceProviderConfiguration();
    }

    public function createWithConfig(Saml2ServiceProviderConfiguration $config): self
    {
        $service = clone $this;
        $service->setConfig($config);

        return $service;
    }

    public function discoverIdpMetadata(): bool
    {
        $idpMetadataXmlUrl = $this->config->getIdentityProviderMetadataUrl();

        if ($idpMetadataXmlUrl === '' || $this->config->getIdentityProviderMetadataXml() !== '') {
            return true;
        }

        $cacheKey = sprintf(
            'heptacom-admin-open-auth_metadata_%s',
            md5($idpMetadataXmlUrl),
        );
        $cachedMetadata = $this->cache->getItem($cacheKey);

        if (!$cachedMetadata->isHit()) {
            try {
                $uri = new Uri($idpMetadataXmlUrl);

                $request = Saml2RequestHelper::prepareRequest(new Request('GET', $uri));
                $response = $this->samlHttpClient->sendRequest($request);

                Saml2RequestHelper::verifyRequestSuccess($request, $response);

                $cachedMetadata->set((string) $response->getBody());

                $cachedMetadata->expiresAfter(self::METADATA_CACHE_TTL);
                $this->cache->save($cachedMetadata);
            } catch (ClientExceptionInterface $e) {
                $message = sprintf(
                    'Could not discover SAML metadata from %s. %s',
                    $idpMetadataXmlUrl,
                    $e->getMessage()
                );
                $this->logger->warning($message, $e->getTrace());

                return false;
            }
        }

        $this->config->setIdentityProviderMetadataXml((string) $cachedMetadata->get());

        return true;
    }

    /**
     * @return string|null The URL the user should be redirected to
     * @throws Error
     */
    public function getAuthnRequestRedirectUri(?string $relayState): string
    {
        $parameters = [];

        $settings = $this->getSaml2Settings();
        $auth = new Auth($this->config->getOneLoginSettings());
        $authnRequest = new AuthnRequest($settings);

        // replace request id
        $samlRequest = $this->decodeRequest($authnRequest->getRequest());

        if ($relayState !== null) {
            $samlRequest = preg_replace('/ID="[^"]+"/', 'ID="' . self::ID_PREFIX . $relayState . '"', $samlRequest, 1);
        }
        $samlRequest = $this->encodeRequest($samlRequest);

        $parameters['SAMLRequest'] = $samlRequest;
        $parameters['RelayState'] = $relayState;

        $security = $settings->getSecurityData();
        if (isset($security['authnRequestsSigned']) && $security['authnRequestsSigned']) {
            $signature = $auth->buildRequestSignature($samlRequest, $relayState, $security['signatureAlgorithm']);
            $parameters['SigAlg'] = $security['signatureAlgorithm'];
            $parameters['Signature'] = $signature;
        }

        $uri = new Uri((string) $settings->getIdPSSOUrl());
        $uri = $uri->withQuery(http_build_query($parameters));

        return (string) $uri;
    }

    /**
     * Creates the SAML Service Provider Metadata XML
     * @return string
     * @throws Error
     */
    public function getServiceProviderMetadata(): string
    {
        // TODO: SAML: Catch errors
        $settings = $this->getSaml2Settings();
        $metadata = $settings->getSPMetadata();
        $errors = $settings->validateMetadata($metadata);

        if (count($errors) > 0) {
            // TODO: SAML: handle error
            throw new Error('Invalid SP metadata: ' . implode(', ', $errors), Error::METADATA_SP_INVALID);
        } else {
            return $metadata;
        }
    }

    // TODO: SAML: specify return type
    public function validateLoginConfirmData(string $samlResponse, string $relayState): Auth
    {
        Saml2Toolkit::prepareSuperGlobals($samlResponse, $relayState);

        try {
            $auth = new Auth($this->config->getOneLoginSettings());
            $auth->processResponse(self::ID_PREFIX . $relayState);
            $errors = $auth->getErrors();

            if (count($errors) > 0) {
                throw new Error('Invalid response: ' . implode(', ', $errors));
            }

            return $auth;
        } finally {
            Saml2Toolkit::restoreSuperGlobals();
        }
    }

    protected function getSaml2Settings(): Settings
    {
        // TODO: SAML: Catch error
        return new Settings($this->config->getOneLoginSettings(), true);
    }

    public function getConfig(): Saml2ServiceProviderConfiguration
    {
        return $this->config;
    }

    public function setConfig(Saml2ServiceProviderConfiguration $config): void
    {
        $this->config = $config;
    }

    protected function decodeRequest(string $payload): string
    {
        $encodedPayload = base64_decode($payload);

        $inflatedPayload = gzinflate($encodedPayload);

        return !$inflatedPayload ? $encodedPayload : $inflatedPayload;
    }

    protected function encodeRequest(string $payload): string
    {
        if ($this->getSaml2Settings()->shouldCompressRequests()) {
            $payload = gzdeflate($payload);
        }

        return base64_encode($payload);
    }
}
