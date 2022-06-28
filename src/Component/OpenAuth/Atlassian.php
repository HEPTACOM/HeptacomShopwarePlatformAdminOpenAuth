<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenAuth;

use Mrjoops\OAuth2\Client\Provider\Jira;

class Atlassian extends Jira
{
    /**
     * @var string[]
     */
    protected array $scopes = [];

    public function getBaseAccessTokenUrl(array $params)
    {
        return 'https://auth.atlassian.com/oauth/token';
    }

    public function getBaseAuthorizationUrl()
    {
        return 'https://auth.atlassian.com/authorize?audience=api.atlassian.com';
    }

    protected function getDefaultScopes()
    {
        return $this->scopes;
    }
}
