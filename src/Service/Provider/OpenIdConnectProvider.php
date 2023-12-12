<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectConfiguration;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectService;
use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\ClientProvider\ClientProviderContract;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OpenIdConnectProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'open_id_connect';

    public function __construct(
        private readonly TokenPairFactoryContract $tokenPairFactory,
        private readonly OpenIdConnectService $openIdConnectService,
    ) {
    }

    public function provides(): string
    {
        return static::PROVIDER_NAME;
    }

    public function getConfigurationTemplate(): OptionsResolver
    {
        return parent::getConfigurationTemplate()
            ->setDefined([
                'id',
                'discoveryDocumentUrl',
                'authorization_endpoint',
                'token_endpoint',
                'userinfo_endpoint',
                'clientId',
                'clientSecret',
                'scopes',
                // TODO remove in v6
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
                'heptacom/shopware-platform-admin-open-auth',
                '*',
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
