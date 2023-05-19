<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Database\LoginCollection;
use Heptacom\AdminOpenAuth\Database\LoginEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

final class Login implements LoginInterface
{
    public function __construct(private readonly EntityRepository $loginsRepository)
    {
    }

    public function initiate(string $clientId, ?string $userId, string $state, Context $context): string
    {
        $id = Uuid::randomHex();
        $this->loginsRepository->create([[
            'id' => $id,
            'clientId' => $clientId,
            'userId' => $userId,
            'state' => $state,
        ]], $context);

        return $id;
    }

    public function setCredentials(string $state, string $userId, Context $context): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('state', $state));
        $ids = $this->loginsRepository->searchIds($criteria, $context);
        $update = [];

        foreach ($ids->getIds() as $id) {
            $update[] = [
                'id' => $id,
                'userId' => $userId,
            ];
        }

        $this->loginsRepository->update($update, $context);

        return $ids->getTotal() > 0;
    }

    public function pop(string $state, Context $context): ?LoginEntity
    {
        $criteria = new Criteria();
        $criteria->addAssociation('user');
        $criteria->addFilter(new EqualsFilter('state', $state));
        /** @var LoginCollection $logins */
        $logins = $this->loginsRepository->search($criteria, $context)->getEntities();

        if ($logins->count() > 0) {
            $deletePayload = $logins->map(fn(LoginEntity $login): array => ['id' => $login->getId()]);
            $this->loginsRepository->delete(\array_values($deletePayload), $context);
        }

        return $logins->first();
    }

    public function getUser(string $state, Context $context): ?string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('state', $state));
        /** @var LoginCollection $logins */
        $logins = $this->loginsRepository->search($criteria, $context)->getEntities();
        $first = $logins->first();

        return $first === null ? null : $first->userId;
    }
}
