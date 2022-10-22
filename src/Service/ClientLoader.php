<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Database\ClientCollection;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\AdminOpenAuth\Exception\LoadClientClientNotFoundException;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Client\Contract\ClientFactoryContract;
use Heptacom\OpenAuth\Client\Exception\FactorizeClientException;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderRepositoryContract;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;

class ClientLoader implements ClientLoaderInterface
{
    private ClientProviderRepositoryContract $providers;

    private EntityRepositoryInterface $clientsRepository;

    private ClientFactoryContract $clientFactory;

    public function __construct(
        ClientProviderRepositoryContract $providers,
        EntityRepositoryInterface $clientsRepository,
        ClientFactoryContract $clientFactory
    ) {
        $this->providers = $providers;
        $this->clientsRepository = $clientsRepository;
        $this->clientFactory = $clientFactory;
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

        try {
            return $this->clientFactory->create($client->getProvider() ?? '', $client->getConfig() ?? []);
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

        $config['clientId'] = $id;

        $this->clientsRepository->create([[
            'id' => $id,
            'name' => $providerKey,
            'provider' => $providerKey,
            'active' => false,
            'config' => $config,
        ]], $context);

        return $id;
    }
}
