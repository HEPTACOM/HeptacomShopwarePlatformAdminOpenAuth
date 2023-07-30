<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\Client\ClientFactoryContract;
use Heptacom\AdminOpenAuth\Contract\Client\Exception\FactorizeClientException;
use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\ClientProvider\ClientProviderContract;
use Heptacom\AdminOpenAuth\Contract\ClientProvider\ClientProviderRepositoryContract;
use Heptacom\AdminOpenAuth\Contract\ConfigurationRefresherClientProviderContract;
use Heptacom\AdminOpenAuth\Database\ClientCollection;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\AdminOpenAuth\Exception\LoadClientClientNotFoundException;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;

final class ClientLoader implements ClientLoaderInterface
{
    public function __construct(
        private readonly ClientProviderRepositoryContract $providers,
        private readonly EntityRepository $clientsRepository,
        private readonly ClientFactoryContract $clientFactory
    ) {
    }

    public function load(string $clientId, Context $context): ClientContract
    {
        $criteria = new Criteria();
        $criteria->setIds([$clientId]);

        /** @var ClientCollection $searchResult */
        $searchResult = $this->clientsRepository->search($criteria, $context)->getEntities();
        $client = $searchResult->first();

        if (!$client instanceof ClientEntity) {
            throw new LoadClientClientNotFoundException($clientId);
        }

        $this->updateClientConfig($client, $context);

        try {
            return $this->clientFactory->create($client->provider ?? '', $client->config ?? []);
        } catch (FactorizeClientException $exception) {
            throw new LoadClientException($exception->getMessage(), $clientId, $exception);
        }
    }

    public function create(string $providerKey, Context $context): string
    {
        $id = Uuid::randomHex();
        $config = [];
        $clientProvider = $this->providers->getMatchingProvider($providerKey);

        if ($clientProvider instanceof ClientProviderContract) {
            $config = $clientProvider->getConfigurationTemplate()->resolve($clientProvider->getInitialConfiguration());
        }

        $config['id'] = $id;

        $this->clientsRepository->create([[
            'id' => $id,
            'name' => $providerKey,
            'provider' => $providerKey,
            'active' => false,
            'config' => $config,
            'userBecomeAdmin' => false,
            'keepUserUpdated' => false,
        ]], $context);

        return $id;
    }

    protected function updateClientConfig(ClientEntity $client, Context $context): void
    {
        $clientProvider = $this->providers->getMatchingProvider($client->provider ?? '');
        $config = $client->config ?? [];

        if (!$clientProvider instanceof ConfigurationRefresherClientProviderContract || !$clientProvider->configurationNeedsUpdate($config)) {
            return;
        }

        $config = $clientProvider->refreshConfiguration($config);
        $client->config = $config;

        $this->clientsRepository->update([[
            'id' => $client->getId(),
            'config' => $config,
        ]], $context);
    }
}
