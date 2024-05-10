<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\UserEmailInterface;
use Heptacom\AdminOpenAuth\Database\UserEmailCollection;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Aggregation\Metric\EntityAggregation;
use Shopware\Core\Framework\DataAbstractionLayer\Search\AggregationResult\Metric\EntityResult;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\User\UserCollection;
use Shopware\Core\System\User\UserDefinition;

final readonly class UserEmail implements UserEmailInterface
{
    /**
     * @param EntityRepository<UserEmailCollection> $userEmailsRepository
     */
    public function __construct(
        private EntityRepository $userEmailsRepository,
    ) {
    }

    public function add(string $userId, string $email, string $clientId, Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('email', $email),
            new EqualsFilter('userId', $userId)
        );
        $exists = $this->userEmailsRepository->searchIds($criteria, $context);

        if ($exists->getTotal() > 0) {
            return $exists->firstId();
        }

        $id = Uuid::randomHex();
        $this->userEmailsRepository->create([[
            'id' => $id,
            'userId' => $userId,
            'email' => $email,
            'clientId' => $clientId,
        ]], $context);

        return $id;
    }

    public function searchUser(array $emails, Context $context): UserCollection
    {
        $criteria = new Criteria();
        $criteria->addAggregation(new EntityAggregation('users', 'userId', UserDefinition::ENTITY_NAME));
        $criteria->addFilter(new EqualsAnyFilter('email', $emails));
        /** @var EntityResult $userEmails */
        $userEmails = $this->userEmailsRepository->aggregate($criteria, $context)->get('users');

        return new UserCollection($userEmails->getEntities());
    }
}
