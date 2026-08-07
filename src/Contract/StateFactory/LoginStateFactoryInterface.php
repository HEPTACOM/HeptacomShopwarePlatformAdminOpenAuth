<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\StateFactory;

use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;

/**
 * Creates the login state that ties a redirect coming back from the identity provider to the request that started it.
 */
interface LoginStateFactoryInterface
{
    /**
     * Creates a login state for the client.
     *
     * @throws LoadClientException
     */
    public function create(string $clientId, ?string $redirectTo, Context $context): string;

    /**
     * Creates a login state carrying additional data, to pass context of the initiating request along the login.
     *
     * @param array<string, mixed> $payload
     *
     * @throws LoadClientException
     */
    public function createWithPayload(string $clientId, ?string $redirectTo, array $payload, Context $context): string;
}
