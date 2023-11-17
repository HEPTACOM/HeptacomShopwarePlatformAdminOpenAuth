<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

interface OpenAuthenticationFlowInterface
{
    /**
     * @throws LoadClientException
     */
    public function upsertUser(User $user, string $clientId, string $state, Context $context): void;

    public function disconnectClient(string $clientId, string $userId, Context $context): void;

    /**
     * @return EntityCollection<ClientEntity>
     */
    public function getAvailableClients(Criteria $criteria, Context $context): EntityCollection;

    public function getLoginRoutes(Context $context): array;
}
