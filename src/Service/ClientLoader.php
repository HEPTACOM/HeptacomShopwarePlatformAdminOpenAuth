<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\ProviderInterface;
use Heptacom\AdminOpenAuth\Database\ClientCollection;
use Heptacom\AdminOpenAuth\Exception\LoadClientClientNotFoundException;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Heptacom\AdminOpenAuth\Exception\LoadClientMatchingProviderNotFoundException;
use Heptacom\AdminOpenAuth\Exception\ProvideClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Traversable;

class ClientLoader implements ClientLoaderInterface
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @var EntityRepositoryInterface
     */
    private $clientsRepository;

    /**
     * @param iterable|Traversable|ProviderInterface[] $providers
     */
    public function __construct(Traversable $providers, EntityRepositoryInterface $clientsRepository)
    {
        $this->providers = iterator_to_array($providers);
        $this->clientsRepository = $clientsRepository;
    }

    public function load(string $clientId, Context $context): ClientInterface
    {
        /** @var ClientCollection $searchResult */
        $searchResult = $this->clientsRepository->search(new Criteria([$clientId]), $context)->getEntities();

        if ($searchResult->count() === 0) {
            throw new LoadClientClientNotFoundException($clientId);
        }

        foreach ($this->providers as $provider) {
            if ($provider->provides() === $searchResult->first()->getProvider()) {
                try {
                    return $provider->provideClient($clientId, $searchResult->first()->getConfig() ?? [], $context);
                } catch (ProvideClientException $e) {
                    throw new LoadClientException($e->getMessage(), $clientId, $e);
                }
            }
        }

        throw new LoadClientMatchingProviderNotFoundException($clientId);
    }

    public function canLogin(string $clientId, Context $context): bool
    {
        $criteria = new Criteria([$clientId]);
        $criteria->addFilter(
            new EqualsFilter('active', true),
            new EqualsFilter('login', true)
        );

        return $this->clientsRepository->searchIds($criteria, $context)->firstId() !== null;
    }

    public function canConnect(string $clientId, Context $context): bool
    {
        $criteria = new Criteria([$clientId]);
        $criteria->addFilter(
            new EqualsFilter('active', true),
            new EqualsFilter('connect', true)
        );

        return $this->clientsRepository->searchIds($criteria, $context)->firstId() !== null;
    }
}
