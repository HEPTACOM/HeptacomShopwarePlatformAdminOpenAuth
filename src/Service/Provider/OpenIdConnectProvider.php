<?php

declare(strict_types = 1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectConfiguration;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectException;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectService;
use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Psr\Http\Client\ClientInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpenIdConnectProvider extends ClientProviderContract
{

    public const PROVIDER_NAME = 'open_id_connect';

    private TokenPairFactoryContract $tokenPairFactory;

    private ClientInterface $oidcHttpClient;

    private AdapterInterface $cache;

    public function __construct(
        TokenPairFactoryContract $tokenPairFactory,
        ClientInterface $oidcHttpClient,
        AdapterInterface $cache
    ) {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->oidcHttpClient = $oidcHttpClient;
        $this->cache = $cache;
    }

    public function provides(): string
    {
        return static::PROVIDER_NAME;
    }

    public function getConfigurationTemplate(): OptionsResolver
    {
        return parent::getConfigurationTemplate()
            ->setDefined([
                'discovery_document_url',
                'authorization_endpoint',
                'token_endpoint',
                'userinfo_endpoint',
                'client_id',
                'client_secret',
                'scopes',
                // TODO remove in v5
                'redirectUri',
            ])->setRequired([
                'discovery_document_url',
                'authorization_endpoint',
                'token_endpoint',
                'userinfo_endpoint',
                'client_id',
                'client_secret',
            ])->setDefaults([
                'scopes' => [],
                'redirectUri' => null,
            ])
            ->setAllowedTypes('discovery_document_url', 'string')
            ->setAllowedTypes('authorization_endpoint', 'string')
            ->setAllowedTypes('token_endpoint', 'string')
            ->setAllowedTypes('userinfo_endpoint', 'string')
            ->setAllowedTypes('client_id', 'string')
            ->setAllowedTypes('client_secret', 'string')
            ->setAllowedTypes('scopes', 'array')
            ->setDeprecated(
                'redirectUri',
                'Use route api.heptacom.admin_open_auth.provider.redirect-url instead to live generate redirectUri'
            );
    }

    public function getInitialConfiguration(): array
    {
        $result = parent::getInitialConfiguration();

        $result['discovery_document_url'] = '';
        $result['authorization_endpoint'] = '';
        $result['token_endpoint'] = '';
        $result['userinfo_endpoint'] = '';
        $result['client_id'] = '';
        $result['client_secret'] = '';

        return $result;
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        $config = new OpenIdConnectConfiguration();
        $config->assign($resolvedConfig);

        $service = new OpenIdConnectService($this->oidcHttpClient, $config, $this->cache);

        try {
            $service->discoverWellKnown();
        } catch (OpenIdConnectException $e) {
            // nth
        }

        return new OpenIdConnectClient($this->tokenPairFactory, $service);
    }
}
