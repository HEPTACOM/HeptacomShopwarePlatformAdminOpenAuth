<?php declare(strict_types=1);

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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class ClientLoader implements ClientLoaderInterface
{
    /**
     * @var ClientProviderRepositoryContract
     */
    private $providers;

    /**
     * @var EntityRepositoryInterface
     */
    private $clientsRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ClientFactoryContract
     */
    private $clientFactory;

    public function __construct(
        ClientProviderRepositoryContract $providers,
        EntityRepositoryInterface $clientsRepository,
        RouterInterface $router,
        ClientFactoryContract $clientFactory
    ) {
        $this->providers = $providers;
        $this->clientsRepository = $clientsRepository;
        $this->router = $router;
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

        // TODO remove from configuration
        $config['redirectUri'] = $this->router->generate('administration.heptacom.admin_open_auth.login', [
            'clientId' => $id,
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->clientsRepository->create([[
            'id' => $id,
            'name' => $providerKey,
            'provider' => $providerKey,
            'active' => false,
            'config' => $config,
            // TODO remove provider key check into service decorator or interface
            'login' => $providerKey === 'jira' || $providerKey === 'microsoft_azure',
            'connect' => $providerKey === 'jira' || $providerKey === 'microsoft_azure',
            'storeUserToken' => $providerKey === 'jira' || $providerKey === 'microsoft_azure',
        ]], $context);

        return $id;
    }
}
