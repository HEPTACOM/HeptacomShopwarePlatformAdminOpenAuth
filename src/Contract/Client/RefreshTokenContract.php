<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\Client;

use Heptacom\AdminOpenAuth\Contract\TokenPair;

/**
 * If implemented, the client supports retrieving a new token from a refresh_token.
 */
interface RefreshTokenContract
{
    public function refreshToken(string $refreshToken): TokenPair;
}
