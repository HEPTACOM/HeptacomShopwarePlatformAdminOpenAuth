<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth;

use Heptacom\AdminOpenAuth\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Contract\ProviderInterface;
use Heptacom\AdminOpenAuth\Database\ClientCollection;
use Heptacom\AdminOpenAuth\Exception\LoadClientClientNotFoundException;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Heptacom\AdminOpenAuth\Exception\LoadClientMatchingProviderNotFoundException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Traversable;

class ClientLoader
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

    /**
     * @throws LoadClientException
     */
    public function load(string $clientId, Context $context): ClientInterface
    {
        /** @var ClientCollection $searchResult */
        $searchResult = $this->clientsRepository->search(new Criteria([$clientId]), $context)->getEntities();

        if ($searchResult->count() === 0) {
            throw new LoadClientClientNotFoundException($clientId);
        }

        foreach ($this->providers as $provider) {
            if ($provider->provides() === $searchResult->first()->getProvider()) {
                return $provider->provideClient($clientId, $searchResult->first()->getConfig() ?? [], $context);
            }
        }

        throw new LoadClientMatchingProviderNotFoundException($clientId);
    }
}
