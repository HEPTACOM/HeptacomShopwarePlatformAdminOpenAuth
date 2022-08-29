<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Database\LoginEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class StateResolver
{
    private EntityRepositoryInterface $loginsRepository;

    public function __construct(EntityRepositoryInterface $loginsRepository)
    {
        $this->loginsRepository = $loginsRepository;
    }

    public function getPayload(string $state, Context $context): ?array
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('state', $state),
        );
        $login = $this->loginsRepository->search($criteria, $context)->first();

        if ($login instanceof LoginEntity) {
            return $login->getPayload();
        }

        return null;
    }
}
