<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Saml;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use OneLogin\Saml2\Auth;
use OneLogin\Saml2\AuthnRequest;
use OneLogin\Saml2\Error as OneLoginSaml2Error;
use OneLogin\Saml2\Settings;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class Saml2ServiceProviderService
{
    public const ID_PREFIX = 'saml2_provider_';
    private const METADATA_CACHE_TTL = 900;

    /**
     * @var array{request: array, get: array, post: array}|null
     */
    private static ?array $superGlobals = null;

    private ClientInterface $samlHttpClient;

    private LoggerInterface $logger;

    private AdapterInterface $cache;

    private Saml2ServiceProviderConfiguration $config;

    public function __construct(
        ClientInterface $samlHttpClient,
        LoggerInterface $logger,
        AdapterInterface $cache
    ) {
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
     * @throws Saml2Exception
     *
     * @return string|null The URL the user should be redirected to
     */
    public function getAuthnRequestRedirectUri(?string $relayState): string
    {
        try {
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
        } catch (\Exception $e) {
            $message = 'Could not build redirect URL';

            if ($e instanceof Saml2Exception) {
                $message .= ': ' . $e->getMessage();
            }

            $this->logger->error($message, $e->getTrace());

            throw new Saml2Exception($message);
        }
    }

    /**
     * Creates the SAML Service Provider Metadata XML
     *
     * @throws Saml2Exception
     */
    public function getServiceProviderMetadata(): string
    {
        try {
            $settings = $this->getSaml2Settings();
            $metadata = $settings->getSPMetadata();
            $errors = $settings->validateMetadata($metadata);

            if (\count($errors) > 0) {
                $errorMessage = sprintf('Invalid SP metadata: %s', implode(', ', $errors));

                throw new OneLoginSaml2Error($errorMessage, OneLoginSaml2Error::METADATA_SP_INVALID);
            }

            return $metadata;
        } catch (\Exception $e) {
            $message = 'Could not retrieve SP metadata';
            $this->logger->critical($message . ': ' . $e->getMessage(), $e->getTrace());

            throw new Saml2Exception($message, $e);
        }
    }

    /**
     * Parse and verify incoming SAMLResponse
     *
     * @throws Saml2Exception
     */
    public function validateLoginConfirmData(string $samlResponse, string $relayState): Auth
    {
        $this->prepareSuperGlobals($samlResponse, $relayState);

        try {
            $auth = new Auth($this->config->getOneLoginSettings());
            $auth->processResponse(self::ID_PREFIX . $relayState);
            $errors = $auth->getErrors();

            if (\count($errors) > 0) {
                throw new OneLoginSaml2Error('Invalid response: ' . implode(', ', $errors));
            }

            return $auth;
        } catch (\Exception $e) {
            $message = sprintf('Could not verify SAMLResponse: %s', $e->getMessage());
            $this->logger->error($message, $e->getTrace());

            throw new Saml2Exception($message, $e);
        } finally {
            $this->restoreSuperGlobals();
        }
    }

    public function getConfig(): Saml2ServiceProviderConfiguration
    {
        return $this->config;
    }

    public function setConfig(Saml2ServiceProviderConfiguration $config): void
    {
        $this->config = $config;
    }

    protected function getSaml2Settings(): Settings
    {
        try {
            return new Settings($this->config->getOneLoginSettings(), true);
        } catch (\Exception $e) {
            $message = sprintf('Could not retrieve SAML settings: %s', $e->getMessage());
            $this->logger->critical($message, $e->getTrace());

            throw new Saml2Exception($message, $e);
        }
    }

    protected function decodeRequest(string $payload): string
    {
        $encodedPayload = base64_decode($payload, true);

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

    /**
     * SAML library relies on super globals, so we need to set them.
     *
     * @throws \Exception if super globals are already set
     */
    protected function prepareSuperGlobals(string $samlResponse, string $relayState): void
    {
        if (self::$superGlobals !== null) {
            throw new \Exception('Super globals are already set. You cannot declare them multiple times.');
        }

        self::$superGlobals = [
            'request' => $_REQUEST,
            'get' => $_GET,
            'post' => $_POST,
        ];

        $_GET = [];
        $_POST = [
            'SAMLResponse' => $samlResponse,
            'RelayState' => $relayState,
        ];

        $_REQUEST = \array_merge($_GET, $_POST);
    }

    /**
     * When we are done with super globals, we want to reset them to their original contents.
     */
    protected function restoreSuperGlobals(): void
    {
        if (self::$superGlobals === null) {
            return;
        }

        $_REQUEST = self::$superGlobals['request'];
        $_GET = self::$superGlobals['get'];
        $_POST = self::$superGlobals['post'];

        self::$superGlobals = null;
    }
}
