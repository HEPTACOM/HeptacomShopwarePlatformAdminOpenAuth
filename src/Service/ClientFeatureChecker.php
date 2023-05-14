<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

final class ClientFeatureChecker implements ClientFeatureCheckerInterface
{
    public function __construct(private readonly EntityRepository $clientsRepository)
    {
    }

    public function canLogin(string $clientId, Context $context): bool
    {
        $criteria = new Criteria();
        $criteria->setIds([$clientId]);
        $criteria->addFilter(
            new EqualsFilter('active', true),
            new EqualsFilter('login', true)
        );

        return $this->clientsRepository->searchIds($criteria, $context)->firstId() !== null;
    }

    public function canConnect(string $clientId, Context $context): bool
    {
        $criteria = new Criteria();
        $criteria->setIds([$clientId]);
        $criteria->addFilter(
            new EqualsFilter('active', true),
            new EqualsFilter('connect', true)
        );

        return $this->clientsRepository->searchIds($criteria, $context)->firstId() !== null;
    }

    public function canStoreUserTokens(string $clientId, Context $context): bool
    {
        $criteria = new Criteria();
        $criteria->setIds([$clientId]);
        $criteria->addFilter(new EqualsFilter('storeUserToken', true));

        return $this->clientsRepository->searchIds($criteria, $context)->firstId() !== null;
    }

    public function canUsersBecomeAdmin(string $clientId, Context $context): bool
    {
        $criteria = new Criteria();
        $criteria->setIds([$clientId]);
        $criteria->addFilter(new EqualsFilter('userBecomeAdmin', true));

        return $this->clientsRepository->searchIds($criteria, $context)->firstId() !== null;
    }

    public function canKeepUserUpdated(string $clientId, Context $context): bool
    {
        $criteria = new Criteria();
        $criteria->setIds([$clientId]);
        $criteria->addFilter(new EqualsFilter('keepUserUpdated', true));

        return $this->clientsRepository->searchIds($criteria, $context)->firstId() !== null;
    }
}
