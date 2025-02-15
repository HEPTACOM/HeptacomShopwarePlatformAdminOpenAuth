<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Database\LoginCollection;
use Heptacom\AdminOpenAuth\Database\LoginEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;

final readonly class Login implements LoginInterface
{
    /**
     * @param EntityRepository<LoginCollection> $loginsRepository
     */
    public function __construct(
        private EntityRepository $loginsRepository,
    ) {
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
        $currentTime = new \DateTime();

        $expiredCriteria = new Criteria();
        $expiredCriteria->addFilter(new RangeFilter('expiresAt', [
            RangeFilter::LT => $currentTime->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]));

        /** @var LoginCollection $expiredLogins */
        $expiredLogins = $this->loginsRepository->search($expiredCriteria, $context)->getEntities();

        if ($expiredLogins->count() > 0) {
            $deletePayload = $expiredLogins->map(fn (LoginEntity $login) => ['id' => $login->getId()]);
            $this->loginsRepository->delete(\array_values($deletePayload), $context);
        }

        $criteria = new Criteria();
        $criteria->addAssociation('user');
        $criteria->addFilter(new EqualsFilter('state', $state));
        $criteria->addFilter(new RangeFilter('expiresAt', [
            RangeFilter::GTE => $currentTime->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]));

        /** @var LoginCollection $logins */
        $logins = $this->loginsRepository->search($criteria, $context)->getEntities();

        if ($logins->count() > 0) {
            return $logins->first();
        }

        return null;
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
