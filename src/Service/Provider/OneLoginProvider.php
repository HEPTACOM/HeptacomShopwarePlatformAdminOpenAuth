<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use GuzzleHttp\Psr7\Uri;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectConfiguration;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectService;
use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\ClientProvider\ClientProviderContract;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class OneLoginProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'onelogin';

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
                'organizationUrl',
                'clientId',
                'clientSecret',
                'scopes',
            ])->setRequired([
                'organizationUrl',
                'clientId',
                'clientSecret',
            ])->setDefaults([
                'scopes' => [],
            ])
            ->setAllowedTypes('organizationUrl', 'string')
            ->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('scopes', 'array');
    }

    public function getInitialConfiguration(): array
    {
        $result = parent::getInitialConfiguration();

        $result['organizationUrl'] = '';
        $result['clientId'] = '';
        $result['clientSecret'] = '';

        return $result;
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        $organizationUrl = new Uri($resolvedConfig['organizationUrl']);

        $config = new OpenIdConnectConfiguration();
        $config->assign($resolvedConfig);
        $config->setDiscoveryDocumentUrl(sprintf('%s://%s/oidc/2/.well-known/openid-configuration', $organizationUrl->getScheme(), $organizationUrl->getHost()));

        $service = $this->openIdConnectService->createWithConfig($config);
        $service->discoverWellKnown();

        return new OpenIdConnectClient($this->tokenPairFactory, $service);
    }
}
