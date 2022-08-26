<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectConfiguration;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectService;
use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpenIdConnectProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'open_id_connect';

    private TokenPairFactoryContract $tokenPairFactory;

    private OpenIdConnectService $openIdConnectService;

    public function __construct(
        TokenPairFactoryContract $tokenPairFactory,
        OpenIdConnectService $openIdConnectService
    ) {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->openIdConnectService = $openIdConnectService;
    }

    public function provides(): string
    {
        return static::PROVIDER_NAME;
    }

    public function getConfigurationTemplate(): OptionsResolver
    {
        return parent::getConfigurationTemplate()
            ->setDefined([
                'discoveryDocumentUrl',
                'authorization_endpoint',
                'token_endpoint',
                'userinfo_endpoint',
                'clientId',
                'clientSecret',
                'scopes',
                // TODO remove in v5
                'redirectUri',
            ])->setRequired([
                'discoveryDocumentUrl',
                'authorization_endpoint',
                'token_endpoint',
                'userinfo_endpoint',
                'clientId',
                'clientSecret',
            ])->setDefaults([
                'scopes' => [],
                'redirectUri' => null,
            ])
            ->setAllowedTypes('discoveryDocumentUrl', 'string')
            ->setAllowedTypes('authorization_endpoint', 'string')
            ->setAllowedTypes('token_endpoint', 'string')
            ->setAllowedTypes('userinfo_endpoint', 'string')
            ->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('scopes', 'array')
            ->setDeprecated(
                'redirectUri',
                'Use route api.heptacom.admin_open_auth.provider.redirect-url instead to live generate redirectUri'
            );
    }

    public function getInitialConfiguration(): array
    {
        $result = parent::getInitialConfiguration();

        $result['discoveryDocumentUrl'] = '';
        $result['authorization_endpoint'] = '';
        $result['token_endpoint'] = '';
        $result['userinfo_endpoint'] = '';
        $result['clientId'] = '';
        $result['clientSecret'] = '';

        return $result;
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        $config = new OpenIdConnectConfiguration();
        $config->assign($resolvedConfig);

        $service = $this->openIdConnectService->createWithConfig($config);
        $service->discoverWellKnown();

        return new OpenIdConnectClient($this->tokenPairFactory, $service);
    }
}
