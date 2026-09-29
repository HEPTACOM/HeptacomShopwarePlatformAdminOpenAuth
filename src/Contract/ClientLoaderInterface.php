<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

/**
 * Resolves configured clients into the provider implementation that performs the login.
 */
interface ClientLoaderInterface
{
    /**
     * Loads the client with the given id.
     *
     * @throws LoadClientException
     */
    public function load(string $clientId, Context $context): ClientContract;

    /**
     * Loads the first client matching the criteria, to constrain a client beyond its id.
     *
     * @param Criteria<string> $criteria
     *
     * @throws LoadClientException
     */
    public function loadFromCriteria(Criteria $criteria, Context $context): ClientContract;

    /**
     * Creates a new, inactive client for the given provider and returns its id.
     */
    public function create(string $providerKey, Context $context): string;
}
