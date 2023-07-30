<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\Client;

use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Client\Exception\FactorizeClientInvalidConfigurationException;
use Heptacom\OpenAuth\Client\Exception\FactorizeClientProviderFailedException;
use Heptacom\OpenAuth\Client\Exception\FactorizeClientProviderNotFoundException;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderRepositoryContract;
use Heptacom\OpenAuth\ClientProvider\Exception\ProvideClientException;

class ClientFactoryContract
{
    public function __construct(
        private ClientProviderRepositoryContract $clientProviderRepository
    ) {
    }

    public function create(string $providerKey, array $configuration): ClientContract
    {
        $clientProvider = $this->clientProviderRepository->getMatchingProvider($providerKey);

        if (!$clientProvider instanceof ClientProviderContract) {
            throw new FactorizeClientProviderNotFoundException($providerKey);
        }

        try {
            $resolvedConfig = $clientProvider->getConfigurationTemplate()->resolve($configuration);
        } catch (\Throwable $e) {
            throw new FactorizeClientInvalidConfigurationException($providerKey, $configuration, $e);
        }

        try {
            return $clientProvider->provideClient($resolvedConfig);
        } catch (ProvideClientException $e) {
            throw new FactorizeClientProviderFailedException($providerKey, $e);
        }
    }
}
