<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\UserKeyInterface;
use Heptacom\AdminOpenAuth\Database\UserKeyCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric\EntityResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\User\UserCollection;
use Shopware\Core\System\User\UserDefinition;

final readonly class UserKey implements UserKeyInterface
{
    /**
     * @param EntityRepository<UserKeyCollection> $userKeysRepository
     */
    public function __construct(
        private EntityRepository $userKeysRepository,
    ) {
    }

    public function add(string $userId, string $primaryKey, string $clientId, Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('primaryKey', $primaryKey),
            new EqualsFilter('userId', $userId),
            new EqualsFilter('clientId', $clientId)
        );
        $exists = $this->userKeysRepository->searchIds($criteria, $context);

        if ($exists->getTotal() > 0) {
            return $exists->firstId();
        }

        $id = Uuid::randomHex();
        $this->userKeysRepository->create([[
            'id' => $id,
            'userId' => $userId,
            'primaryKey' => $primaryKey,
            'clientId' => $clientId,
        ]], $context);

        return $id;
    }

    public function searchUser(string $primaryKey, string $clientId, Context $context): UserCollection
    {
        $criteria = new Criteria();
        $criteria->addAggregation(new EntityAggregation('users', 'userId', UserDefinition::ENTITY_NAME));
        $criteria->addFilter(
            new EqualsFilter('primaryKey', $primaryKey),
            new EqualsFilter('clientId', $clientId)
        );
        /** @var EntityResult $userKeys */
        $userKeys = $this->userKeysRepository->aggregate($criteria, $context)->get('users');

        return new UserCollection($userKeys->getEntities());
    }
}
