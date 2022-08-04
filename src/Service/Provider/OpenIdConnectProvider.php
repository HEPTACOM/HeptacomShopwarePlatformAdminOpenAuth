<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OpenIdConnectProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'open_id_connect';

    private TokenPairFactoryContract $tokenPairFactory;

    public function __construct(TokenPairFactoryContract $tokenPairFactory)
    {
        $this->tokenPairFactory = $tokenPairFactory;
    }

    public function provides(): string
    {
        return static::PROVIDER_NAME;
    }

    public function getConfigurationTemplate(): OptionsResolver
    {
        return parent::getConfigurationTemplate()
            ->setDefined([
                'provider_url',
                'authorization_endpoint',
                'token_endpoint',
                'userinfo_endpoint',
                'client_id',
                'client_secret',
                'scopes',
                // TODO remove in v5
                'redirectUri',
            ])->setRequired([
                'provider_url',
                'authorization_endpoint',
                'token_endpoint',
                'userinfo_endpoint',
                'client_id',
                'client_secret',
            ])->setDefaults([
                'scopes' => [],
                'redirectUri' => null,
            ])
            ->setAllowedTypes('provider_url', 'string')
            ->setAllowedTypes('authorization_endpoint', 'string')
            ->setAllowedTypes('token_endpoint', 'string')
            ->setAllowedTypes('userinfo_endpoint', 'string')
            ->setAllowedTypes('client_id', 'string')
            ->setAllowedTypes('client_secret', 'string')
            ->setAllowedTypes('scopes', 'array')
            ->setDeprecated('redirectUri', 'Use route api.heptacom.admin_open_auth.provider.redirect-url instead to live generate redirectUri');
    }

    public function getInitialConfiguration(): array
    {
        $result = parent::getInitialConfiguration();

        $result['provider_url'] = '';
        $result['authorization_endpoint'] = '';
        $result['token_endpoint'] = '';
        $result['userinfo_endpoint'] = '';
        $result['client_id'] = '';
        $result['client_secret'] = '';

        return $result;
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        return new OpenIdConnectClient($this->tokenPairFactory, $resolvedConfig);
    }
}
