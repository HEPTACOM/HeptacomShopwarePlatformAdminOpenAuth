<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Contract\UserResolverInterface;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class OpenAuthenticationFlow implements OpenAuthenticationFlowInterface
{
    public function __construct(
        private UserResolverInterface $userResolver,
        private EntityRepository $clientsRepository,
        private EntityRepository $loginsRepository,
        private EntityRepository $userEmailsRepository,
        private EntityRepository $userKeysRepository,
        private EntityRepository $userTokensRepository,
        private RouterInterface $router,
    ) {
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
