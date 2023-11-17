<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviourFactoryInterface;
use Heptacom\AdminOpenAuth\Contract\StateFactory\LoginStateFactoryInterface;
use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Contract\UserResolverInterface;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class OpenAuthenticationFlow implements OpenAuthenticationFlowInterface
{
    public function __construct(
        private readonly LoginInterface $login,
        private readonly ClientLoaderInterface $clientLoader,
        private readonly UserResolverInterface $userResolver,
        private readonly EntityRepository $clientsRepository,
        private readonly EntityRepository $loginsRepository,
        private readonly EntityRepository $userEmailsRepository,
        private readonly EntityRepository $userKeysRepository,
        private readonly EntityRepository $userTokensRepository,
        private readonly RouterInterface $router,
        private readonly RedirectBehaviourFactoryInterface $redirectBehaviourFactory,
        private readonly ClientFeatureCheckerInterface $clientFeatureChecker,
        private readonly LoginStateFactoryInterface $loginStateFactory,
    ) {
    }

    public function getRedirectUrl(string $clientId, ?string $redirectTo, Context $context): string
    {
        $state = $this->loginStateFactory->create($clientId, $redirectTo, $context);

        return $this->clientLoader->load($clientId, $context)
            ->getLoginUrl($state, $this->redirectBehaviourFactory->createRedirectBehaviour($clientId, $context));
    }

    public function getRedirectUrlToConnect(string $clientId, string $userId, ?string $redirectTo, Context $context): string
    {
        if (!$this->clientFeatureChecker->canConnect($clientId, $context)) {
            throw new LoadClientException('Client can not connect', $clientId);
        }

        $state = Uuid::randomHex();
        $this->login->initiate($clientId, $userId, $state, 'connect', $redirectTo, $context);

        return $this->clientLoader->load($clientId, $context)
            ->getLoginUrl($state, $this->redirectBehaviourFactory->createRedirectBehaviour($clientId, $context));
    }

    public function upsertUser(User $user, string $clientId, string $state, Context $context): void
    {
        $this->userResolver->resolve($user, $state, $clientId, $context);
    }

    public function disconnectClient(string $clientId, string $userId, Context $context): void
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('clientId', $clientId),
            new EqualsFilter('userId', $userId)
        );

        $repos = [
            $this->loginsRepository,
            $this->userEmailsRepository,
            $this->userKeysRepository,
            $this->userTokensRepository,
        ];

        /** @var EntityRepository $repo */
        foreach ($repos as $repo) {
            $ids = $repo->searchIds($criteria, $context)->getIds();

            if (\count($ids) === 0) {
                continue;
            }

            $repo->delete(
                \array_map(static fn (string $id): array => ['id' => $id], $ids),
                $context
            );
        }
    }

    public function getAvailableClients(Criteria $criteria, Context $context): EntityCollection
    {
        $criteria = clone $criteria;
        $criteria->addFilter(
            new EqualsFilter('active', true),
            new EqualsFilter('login', true)
        );
        $criteria->addSorting(new FieldSorting('name', FieldSorting::ASCENDING));

        return $this->clientsRepository
            ->search($criteria, $context)
            ->getEntities();
    }

    public function getLoginRoutes(Context $context): array
    {
        return \array_values($this->getAvailableClients(new Criteria(), $context)
            ->map(fn (ClientEntity $client): array => [
                'name' => $client->name,
                'url' => $this->router->generate(
                    'administration.heptacom.admin_open_auth.remote_login',
                    ['clientId' => $client->getId()],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ]));
    }
}
