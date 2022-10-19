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

    private string $appSecret;

    private TokenPairFactoryContract $tokenPairFactory;

    private Saml2ServiceProviderService $saml2ServiceProviderService;

    public function __construct(
        string $appSecret,
        TokenPairFactoryContract $tokenPairFactory,
        Saml2ServiceProviderService $saml2ServiceProviderService
    ) {
        $this->appSecret = $appSecret;
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
            ->setAllowedTypes('metadataUrl', 'string')
            ->setAllowedTypes('identityProviderUrl', 'string')
            ->setAllowedTypes('identityProviderCertificate', 'string')
            ->setAllowedTypes('serviceProviderPrivateKey', 'string')
            ->setAllowedTypes('serviceProviderPublicKey', 'string')
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

        // TODO: tag:v5.0.0 make generation configurable and dynamic per client in administration
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($privateKey, $result['serviceProviderPrivateKey'], $this->appSecret);
        $result['serviceProviderPublicKey'] = openssl_pkey_get_details($privateKey)['key'];

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
