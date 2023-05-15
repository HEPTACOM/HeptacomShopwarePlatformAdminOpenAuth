<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Provider\MicrosoftAzureClient;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @deprecated tag:v5.0.0 will be replaced by microsoft_azure_oidc provider
 */
final class MicrosoftAzureProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'microsoft_azure';

    public function __construct(private readonly TokenPairFactoryContract $tokenPairFactory)
    {
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

        $result['clientId'] = '';
        $result['clientSecret'] = '';

        return $result;
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        return new MicrosoftAzureClient($this->tokenPairFactory, $resolvedConfig);
    }
}
