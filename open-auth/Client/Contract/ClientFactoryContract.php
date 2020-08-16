<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Client\Contract;

use Heptacom\OpenAuth\Client\Exception\FactorizeClientInvalidConfigurationException;
use Heptacom\OpenAuth\Client\Exception\FactorizeClientProviderFailedException;
use Heptacom\OpenAuth\Client\Exception\FactorizeClientProviderNotFoundException;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderRepositoryContract;
use Heptacom\OpenAuth\ClientProvider\Exception\ProvideClientException;

class ClientFactoryContract
{
    /**
     * @var ClientProviderRepositoryContract
     */
    private $clientProviderRepository;

    public function __construct(ClientProviderRepositoryContract $clientProviderRepository)
    {
        $this->clientProviderRepository = $clientProviderRepository;
    }

    public function create(string $providerKey, array $configuration): ClientContract
    {
        $clientProvider = $this->clientProviderRepository->getMatchingProvider($providerKey);

        if (!$clientProvider instanceof ClientProviderContract) {
            throw new FactorizeClientProviderNotFoundException($providerKey);
        }

        try {
            $resolvedConfig = $clientProvider->getConfigurationTemplate()->resolve($$configuration);
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
