<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\OpenAuth;

use Mrjoops\OAuth2\Client\Provider\Jira;

class Atlassian extends Jira
{
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
        return [
            'read:me',
            'read:jira-user',
        ];
    }
}
