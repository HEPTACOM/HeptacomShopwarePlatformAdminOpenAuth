<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Provider\Saml2ServiceProviderClient;
use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderConfiguration;
use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderService;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class Saml2ServiceProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'saml2';

    private string $appSecret;

    private TokenPairFactoryContract $tokenPairFactory;

    private Saml2ServiceProviderService $saml2ServiceProviderService;

    private RouterInterface $router;

    public function __construct(
        string $appSecret,
        TokenPairFactoryContract $tokenPairFactory,
        Saml2ServiceProviderService $saml2ServiceProviderService,
        RouterInterface $router
    ) {
        $this->appSecret = $appSecret;
        $this->tokenPairFactory = $tokenPairFactory;
        $this->saml2ServiceProviderService = $saml2ServiceProviderService;
        $this->router = $router;
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
                'identityProviderMetadataUrl',
                'identityProviderMetadataXml',
                'identityProviderEntityId',
                'identityProviderSsoUrl',
                'identityProviderCertificate',
                'serviceProviderCertificate',
                'serviceProviderPrivateKey',
                'serviceProviderPublicKey',
                'attributeMapping',
                // TODO remove in v5
                'redirectUri',
            ])->setRequired([
                'identityProviderMetadataUrl',
                'identityProviderMetadataXml',
                'identityProviderEntityId',
                'identityProviderSsoUrl',
                'identityProviderCertificate',
                'serviceProviderCertificate',
                'serviceProviderPrivateKey',
                'serviceProviderPublicKey',
            ])->setDefaults([
                'attributeMapping' => [],
                'redirectUri' => null,
            ])
            ->setAllowedTypes('identityProviderMetadataUrl', 'string')
            ->setAllowedTypes('identityProviderMetadataXml', 'string')
            ->setAllowedTypes('identityProviderEntityId', 'string')
            ->setAllowedTypes('identityProviderSsoUrl', 'string')
            ->setAllowedTypes('identityProviderCertificate', 'string')
            ->setAllowedTypes('serviceProviderCertificate', 'string')
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

        $result['identityProviderMetadataUrl'] = '';
        $result['identityProviderMetadataXml'] = '';
        $result['identityProviderEntityId'] = '';
        $result['identityProviderSsoUrl'] = '';
        $result['identityProviderCertificate'] = '';

        $result['attributeMapping'] = array_combine(
            Saml2ServiceProviderClient::AVAILABLE_USER_PROPERTIES,
            array_fill(0, count(Saml2ServiceProviderClient::AVAILABLE_USER_PROPERTIES), '')
        );

        // TODO: tag:v5.0.0 make generation configurable and dynamic per client in administration
        // TODO: SAML: implement auto renew
        $this->createCertificate($result);

        return $result;
    }

    public function createCertificate(array &$config): void
    {
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => \OPENSSL_KEYTYPE_RSA,
        ]);

        openssl_pkey_export($privateKey, $config['serviceProviderPrivateKey'], $this->appSecret);
        $config['serviceProviderPublicKey'] = openssl_pkey_get_details($privateKey)['key'];

        $csr = openssl_csr_new([], $privateKey, ['digest_alg' => 'sha256']);
        $x509 = openssl_csr_sign($csr, null, $privateKey, 365, ['digest_alg' => 'sha256']);
        openssl_x509_export($x509, $config['serviceProviderCertificate']);
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        $config = new Saml2ServiceProviderConfiguration();
        $config->assign($resolvedConfig);

        // decrypt private key, as library doesn't support encrypted keys
        $privateKey = openssl_get_privatekey($config->getServiceProviderPrivateKey(), $this->appSecret);
        if (!$privateKey) {
            throw new \RuntimeException('SP private key could not be loaded. Refresh of private key is required.');
        }
        openssl_pkey_export($privateKey, $decryptedPrivateKey);
        $config->setServiceProviderPrivateKey($decryptedPrivateKey);

        // Add routes to config
        $config->setServiceProviderEntityId($this->router->generate('administration.heptacom.admin_open_auth.metadata', [
            'clientId' => $resolvedConfig['id'] ?? self::PROVIDER_NAME,
        ], UrlGeneratorInterface::ABSOLUTE_URL));

        $config->setServiceProviderAssertionUrl($this->router->generate('administration.heptacom.admin_open_auth.login', [
            'clientId' => $resolvedConfig['id'] ?? self::PROVIDER_NAME,
        ], UrlGeneratorInterface::ABSOLUTE_URL));

        // create client and discover IdP metadata
        $service = $this->saml2ServiceProviderService->createWithConfig($config);
        $service->discoverIdpMetadata();

        return new Saml2ServiceProviderClient($this->tokenPairFactory, $service);
    }
}
