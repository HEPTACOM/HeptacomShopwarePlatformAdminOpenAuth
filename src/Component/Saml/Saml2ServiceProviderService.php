<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Saml;

class Saml2ServiceProviderService
{
    // TODO: SAML: implement

    private Saml2ServiceProviderConfiguration $config;

    public function createWithConfig(Saml2ServiceProviderConfiguration $config): self
    {
        $service = clone $this;
        $service->setConfig($config);

        return $service;
    }

    public function discoverMetadata(): void
    {
        // TODO: SAML: implement
    }

    /**
     * @return string base64 encoded SAML request
     */
    public function buildSamlRequest(string $redirectUri): string
    {
        // TODO: SAML: implement
    }

    public function getConfig(): Saml2ServiceProviderConfiguration
    {
        return $this->config;
    }

    public function setConfig(Saml2ServiceProviderConfiguration $config): void
    {
        $this->config = $config;
    }
}
