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

final class MicrosoftAzureOidcProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'microsoft_azure_oidc';

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
                'tenantId',
                'clientId',
                'clientSecret',
                'scopes',
            ])->setRequired([
                'tenantId',
                'clientId',
                'clientSecret',
            ])->setDefaults([
                'scopes' => [],
            ])
            ->setAllowedTypes('tenantId', 'string')
            ->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('scopes', 'array');
    }

    public function getInitialConfiguration(): array
    {
        $result = parent::getInitialConfiguration();

        $result['tenantId'] = '';
        $result['clientId'] = '';
        $result['clientSecret'] = '';

        return $result;
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        $config = new OpenIdConnectConfiguration();
        $config->assign($resolvedConfig);
        $config->setDiscoveryDocumentUrl('https://login.microsoftonline.com/' . $resolvedConfig['tenantId'] . '/v2.0/.well-known/openid-configuration');

        $service = $this->openIdConnectService->createWithConfig($config);
        $service->discoverWellKnown();

        return new OpenIdConnectClient($this->tokenPairFactory, $service);
    }
}
