<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\OpenAuth;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class OneTimeTokenScopeRepository implements ScopeRepositoryInterface
{
    private ScopeRepositoryInterface $decorated;

    public function __construct(ScopeRepositoryInterface $decorated)
    {
        $this->decorated = $decorated;
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
