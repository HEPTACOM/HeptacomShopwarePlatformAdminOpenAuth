<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class StateResolver
{
    public function __construct(private readonly EntityRepository $loginsRepository)
    {
    }

    public function getPayload(string $state, Context $context): ?array
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('state', $state),
        );
        $login = $this->loginsRepository->search($criteria, $context)->first();

        return $login?->payload;
    }
}
