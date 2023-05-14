<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\OpenAuth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

final class OneTimeTokenScopeRepository implements ScopeRepositoryInterface
{
    public function __construct(private readonly ScopeRepositoryInterface $decorated)
    {
    }

    public function getScopeEntityByIdentifier($identifier)
    {
        return $this->decorated->getScopeEntityByIdentifier($identifier);
    }

    public function finalizeScopes(array $scopes, $grantType, ClientEntityInterface $clientEntity, $userIdentifier = null)
    {
        if ($grantType === 'heptacom_admin_open_auth_one_time_token') {
            $grantType = 'password';
        }

        return $this->decorated->finalizeScopes($scopes, $grantType, $clientEntity);
    }
}
