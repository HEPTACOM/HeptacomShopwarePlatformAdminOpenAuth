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

final class GoogleCloudProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'google_cloud';

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
                'clientId',
                'clientSecret',
                'scopes',
            ])->setRequired([
                'clientId',
                'clientSecret',
            ])->setDefaults([
                'scopes' => [],
            ])
            ->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('scopes', 'array');
    }

    public function getInitialConfiguration(): array
    {
        $result = parent::getInitialConfiguration();

        $result['clientId'] = '';
        $result['clientSecret'] = '';

        return $result;
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        $config = new OpenIdConnectConfiguration();
        $config->assign($resolvedConfig);
        $config->setDiscoveryDocumentUrl('https://accounts.google.com/.well-known/openid-configuration');

        $scopes = $config->getScopes();
        \array_push($scopes, 'email', 'profile');
        $config->setScopes(\array_unique($scopes));

        $service = $this->openIdConnectService->createWithConfig($config);
        $service->discoverWellKnown();

        return new OpenIdConnectClient($this->tokenPairFactory, $service);
    }
}
