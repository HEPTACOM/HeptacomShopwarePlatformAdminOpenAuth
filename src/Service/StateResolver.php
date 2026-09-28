<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Database\LoginCollection;
use Heptacom\AdminOpenAuth\Database\LoginEntity;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;

class StateResolver
{
    /**
     * @param EntityRepository<LoginCollection> $loginsRepository
     */
    public function __construct(
        private readonly EntityRepository $loginsRepository,
    ) {
    }

    public function getPayload(string $state, Context $context): ?array
    {
        return $this->getLogin($state, $context)?->payload;
    }

    public function getLogin(string $state, Context $context): ?LoginEntity
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('state', $state),
        );
        $criteria->addFilter(new RangeFilter('expiresAt', [
            RangeFilter::GTE => (new \DateTimeImmutable())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]));

        return $this->loginsRepository->search($criteria, $context)->getEntities()->first();
    }
}
