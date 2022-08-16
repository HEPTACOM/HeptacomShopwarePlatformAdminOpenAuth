<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectConfiguration;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectException;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectService;
use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GoogleCloudProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'google_cloud';

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
                'clientId',
                'clientSecret',
                'scopes',
                // TODO remove in v5
                'redirectUri',
            ])->setRequired([
                'clientId',
                'clientSecret',
            ])->setDefaults([
                'scopes' => [],
                'redirectUri' => null,
            ])
            ->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('scopes', 'array')
            ->setDeprecated('redirectUri', 'Use route api.heptacom.admin_open_auth.provider.redirect-url instead to live generate redirectUri');
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

        $service = $this->openIdConnectService->createWithConfig($config);

        try {
            $service->discoverWellKnown();
        } catch (OpenIdConnectException $e) {
            // nth
        }

        return new OpenIdConnectClient($this->tokenPairFactory, $service);
    }
}
