<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Provider\Saml2ServiceProviderClient;
use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderConfiguration;
use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderService;
use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\ClientProvider\ClientProviderContract;
use Heptacom\AdminOpenAuth\Contract\ConfigurationRefresherClientProviderContract;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class Saml2ServiceProvider extends ClientProviderContract implements ConfigurationRefresherClientProviderContract
{
    public const PROVIDER_NAME = 'saml2';

    public function __construct(
        private readonly string $appSecret,
        private readonly Saml2ServiceProviderService $saml2ServiceProviderService,
        private readonly RouterInterface $router
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
                'identityProviderMetadataUrl',
                'identityProviderMetadataXml',
                'identityProviderEntityId',
                'identityProviderSsoUrl',
                'identityProviderCertificate',
                'serviceProviderCertificate',
                'serviceProviderCertificateExpiresAt',
                'serviceProviderPrivateKey',
                'serviceProviderPublicKey',
                'requestedAuthnContext',
                'attributeMapping',
                // TODO remove in v6
                'redirectUri',
            ])->setRequired([
                'identityProviderMetadataUrl',
                'identityProviderMetadataXml',
                'identityProviderEntityId',
                'identityProviderSsoUrl',
                'identityProviderCertificate',
                'serviceProviderCertificate',
                'serviceProviderCertificateExpiresAt',
                'serviceProviderPrivateKey',
                'serviceProviderPublicKey',
            ])->setDefaults([
                'requestedAuthnContext' => [],
                'attributeMapping' => [],
                'redirectUri' => null,
            ])
            ->setAllowedTypes('identityProviderMetadataUrl', 'string')
            ->setAllowedTypes('identityProviderMetadataXml', 'string')
            ->setAllowedTypes('identityProviderEntityId', 'string')
            ->setAllowedTypes('identityProviderSsoUrl', 'string')
            ->setAllowedTypes('identityProviderCertificate', 'string')
            ->setAllowedTypes('serviceProviderCertificate', 'string')
            ->setAllowedTypes('serviceProviderCertificateExpiresAt', 'string')
            ->setAllowedTypes('serviceProviderPrivateKey', 'string')
            ->setAllowedTypes('serviceProviderPublicKey', 'string')
            ->setAllowedTypes('requestedAuthnContext', 'array')
            ->setAllowedTypes('rolesAttributeName', 'string')
            ->setAllowedTypes('attributeMapping', 'array')
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

        $result['identityProviderMetadataUrl'] = '';
        $result['identityProviderMetadataXml'] = '';
        $result['identityProviderEntityId'] = '';
        $result['identityProviderSsoUrl'] = '';
        $result['identityProviderCertificate'] = '';

        $result['attributeMapping'] = array_combine(
            Saml2ServiceProviderClient::AVAILABLE_USER_PROPERTIES,
            array_fill(0, \count(Saml2ServiceProviderClient::AVAILABLE_USER_PROPERTIES), '')
        );

        // TODO: tag:v6.1.0 make generation configurable and dynamic per client in administration

        return $this->createCertificate($result);
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

        return new Saml2ServiceProviderClient($service);
    }

    public function configurationNeedsUpdate(array $configuration): bool
    {
        $x509Details = openssl_x509_parse($configuration['serviceProviderCertificate'] ?? '');
        if (!$x509Details) {
            return true;
        }

        $expiresAt = \DateTimeImmutable::createFromFormat('ymdHise', $x509Details['validTo'])->diff(
            new \DateTimeImmutable()
        );

        return $expiresAt->invert === 0 || $expiresAt->days < 30;
    }

    public function refreshConfiguration(array $configuration): array
    {
        return $this->createCertificate($configuration);
    }

    protected function createCertificate(array $config): array
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

        $x509Details = openssl_x509_parse($x509);
        $config['serviceProviderCertificateExpiresAt'] = \DateTimeImmutable::createFromFormat('ymdHise', $x509Details['validFrom'])->format(\DateTimeInterface::ATOM);

        return $config;
    }
}
