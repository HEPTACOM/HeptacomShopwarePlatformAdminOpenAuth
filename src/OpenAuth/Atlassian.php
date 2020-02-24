<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\OpenAuth;

use Mrjoops\OAuth2\Client\Provider\Jira;

class Atlassian extends Jira
{
    /**
     * @var bool
     */
    private $storeToken;

    public function __construct(array $options = [], bool $storeToken = false, array $collaborators = [])
    {
        parent::__construct($options, $collaborators);
        $this->storeToken = $storeToken;
    }

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
        $scopes = [
            'read:me',
            'read:jira-user',
        ];

        if ($this->storeToken) {
            $scopes[] = 'offline_access';
        }

        return $scopes;
    }
}
