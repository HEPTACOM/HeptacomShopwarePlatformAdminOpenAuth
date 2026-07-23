<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\Client;

use Heptacom\AdminOpenAuth\Contract\TokenPair;

/**
 * If implemented, the client supports authentication without a user context.
 */
interface StandaloneClientContract extends RequestAuthorizationContract
{
    /**
     * Obtain a token pair for the client using the configured client credentials.
     *
     * @param string[]|null $scopes The requested scopes for the token. If null, the default scopes will be used.
     */
    public function getClientToken(?array $scopes = null): TokenPair;
}
