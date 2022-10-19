<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectConfiguration;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectService;
use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Component\Provider\Saml2ServiceProviderClient;
use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderConfiguration;
use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderService;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Saml2ServiceProvider extends ClientProviderContract
{
    // TODO: SAML: Implement SAML2 provider

    public const PROVIDER_NAME = 'saml2';

    private TokenPairFactoryContract $tokenPairFactory;

    private Saml2ServiceProviderService $saml2ServiceProviderService;

    public function __construct(
        TokenPairFactoryContract $tokenPairFactory,
        Saml2ServiceProviderService $saml2ServiceProviderService
    ) {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->saml2ServiceProviderService = $saml2ServiceProviderService;
    }

    public function provides(): string
    {
        return static::PROVIDER_NAME;
    }

    public function getConfigurationTemplate(): OptionsResolver
    {
        return parent::getConfigurationTemplate()
            ->setDefined([
                'metadataUrl',
                'identityProviderUrl',
                'identityProviderCertificate',
                'serviceProviderPrivateKey',
                'serviceProviderPublicKey',
                'attributeMapping',
                // TODO remove in v5
                'redirectUri',
            ])->setRequired([
                'metadataUrl',
                'identityProviderUrl',
                'identityProviderCertificate',
                'serviceProviderPrivateKey',
                'serviceProviderPublicKey',
            ])->setDefaults([
                'attributeMapping' => [],
                'redirectUri' => null,
            ])
            ->setAllowedTypes('discoveryDocumentUrl', 'string')
            ->setAllowedTypes('authorization_endpoint', 'string')
            ->setAllowedTypes('token_endpoint', 'string')
            ->setAllowedTypes('userinfo_endpoint', 'string')
            ->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('attributeMapping', 'array')
            ->setDeprecated(
                'redirectUri',
                'Use route api.heptacom.admin_open_auth.provider.redirect-url instead to live generate redirectUri'
            );
    }

    public function getInitialConfiguration(): array
    {
        $result = parent::getInitialConfiguration();

        $result['metadataUrl'] = '';
        $result['identityProviderUrl'] = '';
        $result['identityProviderCertificate'] = '';

        // TODO: SAML: generate private key
        $result['serviceProviderPrivateKey'] = '';
        $result['serviceProviderPublicKey'] = '';

        return $result;
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        $config = new Saml2ServiceProviderConfiguration();
        $config->assign($resolvedConfig);

        $service = $this->saml2ServiceProviderService->createWithConfig($config);
        $service->discoverMetadata();

        return new Saml2ServiceProviderClient($this->tokenPairFactory, $service);
    }
}
