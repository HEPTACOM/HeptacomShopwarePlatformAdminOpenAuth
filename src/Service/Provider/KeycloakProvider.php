<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectConfiguration;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectService;
use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Contract\ClientProvider\ClientProviderContract;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class KeycloakProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'keycloak';

    public function __construct(private readonly TokenPairFactoryContract $tokenPairFactory, private readonly OpenIdConnectService $openIdConnectService)
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
                'keycloakOidcJson',
                'scopes',
                // TODO remove in v5
                'redirectUri',
            ])->setRequired([
                'keycloakOidcJson',
            ])->setDefaults([
                'scopes' => [],
                'redirectUri' => null,
            ])
            ->setAllowedTypes('keycloakOidcJson', 'string')
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

        $result['keycloakOidcJson'] = '';

        return $result;
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        $config = new OpenIdConnectConfiguration();
        $config->assign($resolvedConfig);

        $jsonConfig = @json_decode($resolvedConfig['keycloakOidcJson'] ?? '{}', true) ?? [];
        $config->setDiscoveryDocumentUrl(sprintf('%srealms/%s/.well-known/openid-configuration', $jsonConfig['auth-server-url'] ?? 'https://not-available', $jsonConfig['realm'] ?? 'n.a.'));
        $config->setClientId($jsonConfig['resource'] ?? '');
        $config->setClientSecret(($jsonConfig['credentials'] ?? [])['secret'] ?? '');

        $service = $this->openIdConnectService->createWithConfig($config);
        $service->discoverWellKnown();

        return new OpenIdConnectClient($this->tokenPairFactory, $service);
    }
}
