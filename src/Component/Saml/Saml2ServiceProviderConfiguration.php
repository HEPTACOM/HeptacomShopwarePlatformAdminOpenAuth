<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Saml;

use OneLogin\Saml2\Constants;
use OneLogin\Saml2\IdPMetadataParser;
use Shopware\Core\Framework\Struct\Struct;

class Saml2ServiceProviderConfiguration extends Struct
{
    /**
     * @var string The IdP settings will be fetched from the given metadata document (e.g. https://app.onelogin.com/saml/metadata/123456)
     */
    protected string $identityProviderMetadataUrl = '';

    /**
     * @var string|null The IdP metadata. If null, metadata has not been discovered.
     */
    protected ?string $identityProviderMetadataXml = null;

    /**
     * @var string Entity ID
     */
    protected string $identityProviderEntityId = '';

    /**
     * @var string Login URL
     */
    protected string $identityProviderSsoUrl = '';

    /**
     * @var string X509 cert (base64 encoded)
     */
    protected string $identityProviderCertificate;

    /**
     * @var string Identifier of the SP entity (e.g. http://myapp.com/demo1/metadata.php)
     */
    protected string $serviceProviderEntityId;

    /**
     * @var string Assertion consumer service URL (e.g. http://myapp.com/demo1/index.php?acs)
     */
    protected string $serviceProviderAssertionUrl;

    /**
     * @var string Name identifier used to represent the requested object
     */
    protected string $serviceProviderNameIdFormat = Constants::NAMEID_UNSPECIFIED;

    /**
     * @var string X509 cert (base64 encoded)
     */
    protected string $serviceProviderCertificate;

    /**
     * @var string private key (base64 encoded)
     */
    protected string $serviceProviderPrivateKey;

    /**
     * @var array Mapping for attributes to user properties
     */
    protected array $attributeMapping;

    public function getOneLoginSettings(): array
    {
        if ($this->identityProviderMetadataXml !== null) {
            $idpSettings = IdPMetadataParser::parseXML($this->identityProviderMetadataXml);
        } else {
            $idpSettings = [
                'idp' => [
                    'entityId' => $this->identityProviderEntityId,
                    'singleSignOnService' => [
                        'url' => $this->identityProviderSsoUrl,
                        'binding' => Constants::BINDING_HTTP_REDIRECT,
                    ],
                    'x509cert' => $this->identityProviderCertificate,
                ],
            ];
        }

        return array_merge([
            'strict' => true, // TODO: tag:5.0.0 make configurable
            'debug' => false, // TODO: tag:5.0.0 make configurable
            'sp' => [
                'entityId' => $this->serviceProviderEntityId,
                'assertionConsumerService' => [
                    'url' => $this->serviceProviderAssertionUrl, // TODO: SAML: insert dynamically, using URL builder, while building configuration
                    'binding' => Constants::BINDING_HTTP_POST,
                ],
                'NameIdFormat' => Constants::NAMEID_PERSISTENT,
                'x509cert' => $this->serviceProviderCertificate,
                'privateKey' => $this->serviceProviderPrivateKey,
            ],
            'security' => [
                'rejectUnsolicitedResponsesWithInResponseTo' => true,
                'authnRequestsSigned' => true,
            ],
        ], $idpSettings);
    }

    public function getIdentityProviderMetadataUrl(): string
    {
        return $this->identityProviderMetadataUrl;
    }

    /**
     * @param string $identityProviderMetadataUrl
     * @return Saml2ServiceProviderConfiguration
     */
    public function setIdentityProviderMetadataUrl(string $identityProviderMetadataUrl
    ): Saml2ServiceProviderConfiguration {
        $this->identityProviderMetadataUrl = $identityProviderMetadataUrl;

        return $this;
    }

    public function getIdentityProviderMetadataXml(): ?string
    {
        return $this->identityProviderMetadataXml;
    }

    /**
     * @param string|null $identityProviderMetadataXml
     * @return Saml2ServiceProviderConfiguration
     */
    public function setIdentityProviderMetadataXml(?string $identityProviderMetadataXml
    ): Saml2ServiceProviderConfiguration {
        $this->identityProviderMetadataXml = $identityProviderMetadataXml;

        return $this;
    }

    public function getIdentityProviderEntityId(): string
    {
        return $this->identityProviderEntityId;
    }

    /**
     * @param string $identityProviderEntityId
     * @return Saml2ServiceProviderConfiguration
     */
    public function setIdentityProviderEntityId(string $identityProviderEntityId): Saml2ServiceProviderConfiguration
    {
        $this->identityProviderEntityId = $identityProviderEntityId;

        return $this;
    }

    public function getIdentityProviderSsoUrl(): string
    {
        return $this->identityProviderSsoUrl;
    }

    /**
     * @param string $identityProviderSsoUrl
     * @return Saml2ServiceProviderConfiguration
     */
    public function setIdentityProviderSsoUrl(string $identityProviderSsoUrl): Saml2ServiceProviderConfiguration
    {
        $this->identityProviderSsoUrl = $identityProviderSsoUrl;

        return $this;
    }

    public function getIdentityProviderCertificate(): string
    {
        return $this->identityProviderCertificate;
    }

    /**
     * @param string $identityProviderCertificate
     * @return Saml2ServiceProviderConfiguration
     */
    public function setIdentityProviderCertificate(string $identityProviderCertificate
    ): Saml2ServiceProviderConfiguration {
        $this->identityProviderCertificate = $identityProviderCertificate;

        return $this;
    }

    public function getServiceProviderEntityId(): string
    {
        return $this->serviceProviderEntityId;
    }

    /**
     * @param string $serviceProviderEntityId
     * @return Saml2ServiceProviderConfiguration
     */
    public function setServiceProviderEntityId(string $serviceProviderEntityId): Saml2ServiceProviderConfiguration
    {
        $this->serviceProviderEntityId = $serviceProviderEntityId;

        return $this;
    }

    public function getServiceProviderAssertionUrl(): string
    {
        return $this->serviceProviderAssertionUrl;
    }

    /**
     * @param string $serviceProviderAssertionUrl
     * @return Saml2ServiceProviderConfiguration
     */
    public function setServiceProviderAssertionUrl(string $serviceProviderAssertionUrl
    ): Saml2ServiceProviderConfiguration {
        $this->serviceProviderAssertionUrl = $serviceProviderAssertionUrl;

        return $this;
    }

    public function getServiceProviderNameIdFormat(): string
    {
        return $this->serviceProviderNameIdFormat;
    }

    /**
     * @param string $serviceProviderNameIdFormat
     * @return Saml2ServiceProviderConfiguration
     */
    public function setServiceProviderNameIdFormat(string $serviceProviderNameIdFormat
    ): Saml2ServiceProviderConfiguration {
        $this->serviceProviderNameIdFormat = $serviceProviderNameIdFormat;

        return $this;
    }

    public function getServiceProviderCertificate(): string
    {
        return $this->serviceProviderCertificate;
    }

    /**
     * @param string $serviceProviderCertificate
     * @return Saml2ServiceProviderConfiguration
     */
    public function setServiceProviderCertificate(string $serviceProviderCertificate): Saml2ServiceProviderConfiguration
    {
        $this->serviceProviderCertificate = $serviceProviderCertificate;

        return $this;
    }

    public function getServiceProviderPrivateKey(): string
    {
        return $this->serviceProviderPrivateKey;
    }

    /**
     * @param string $serviceProviderPrivateKey
     * @return Saml2ServiceProviderConfiguration
     */
    public function setServiceProviderPrivateKey(string $serviceProviderPrivateKey): Saml2ServiceProviderConfiguration
    {
        $this->serviceProviderPrivateKey = $serviceProviderPrivateKey;

        return $this;
    }

    public function getAttributeMapping(): array
    {
        return $this->attributeMapping;
    }

    /**
     * @param array $attributeMapping
     * @return Saml2ServiceProviderConfiguration
     */
    public function setAttributeMapping(array $attributeMapping): Saml2ServiceProviderConfiguration
    {
        $this->attributeMapping = $attributeMapping;

        return $this;
    }
}
