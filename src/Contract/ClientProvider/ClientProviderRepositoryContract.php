<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\ClientProvider;

class ClientProviderRepositoryContract
{
    /**
     * @var ClientProviderContract[]
     */
    protected array $clientProviders = [];

    /**
     * @param ClientProviderContract[]|iterable<array-key, ClientProviderContract> $clientProviders
     */
    public function __construct(iterable $clientProviders)
    {
        /** @var mixed|ClientProviderContract $clientProvider */
        foreach ($clientProviders as $clientProvider) {
            if ($clientProvider instanceof ClientProviderContract) {
                $this->clientProviders[] = $clientProvider;
            }
        }
    }

    /**
     * @return ClientProviderContract[]
     */
    public function getProviders(): array
    {
        return $this->clientProviders;
    }

    public function getMatchingProvider(string $key): ?ClientProviderContract
    {
        foreach ($this->clientProviders as $provider) {
            if ($provider->provides() === $key) {
                return $provider;
            }
        }

        return null;
    }

    public function getProviderKeys(): array
    {
        $keys = [];

        foreach ($this->clientProviders as $provider) {
            $keys[] = $provider->provides();
        }

        return \array_values(\array_unique($keys));
    }
}
