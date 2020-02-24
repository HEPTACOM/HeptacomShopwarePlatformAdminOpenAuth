<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Provider;

use Heptacom\AdminOpenAuth\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Contract\UserStruct;
use Heptacom\AdminOpenAuth\OpenAuth\Atlassian;
use Mrjoops\OAuth2\Client\Provider\JiraResourceOwner;

class JiraClient implements ClientInterface
{
    /**
     * @var Atlassian
     */
    private $jiraClient;

    /**
     * @var bool
     */
    private $storeToken;

    public function __construct(string $appId, string $appSecret, string $redirectUri, bool $storeToken)
    {
        $this->jiraClient = new Atlassian([
            'clientId' => $appId,
            'clientSecret' => $appSecret,
            'redirectUri' => $redirectUri,
        ], $storeToken);
        $this->storeToken = $storeToken;
    }

    public function getLoginUrl(string $state): string
    {
        return $this->jiraClient->getAuthorizationUrl(['state' => $state]);
    }

    public function getUser(string $state, string $code): UserStruct
    {
        $scope = 'read:jira-user';

        if ($this->storeToken) {
            $scope .= ' offline_access';
        }

        $token = $this->jiraClient->getAccessToken('authorization_code', [
            'code' => $code,
            'scope' => $scope,
        ]);
        /** @var JiraResourceOwner $user */
        $user = $this->jiraClient->getResourceOwner($token);

        return (new UserStruct())
            ->setPrimaryKey($user->getId())
            ->setRefreshToken($this->storeToken ? $token->getRefreshToken() : null)
            ->setAccessToken($this->storeToken ? $token->getToken() : null)
            ->setDisplayName($user->getName())
            ->setPrimaryEmail($user->getEmail())
            ->setEmails([]);
    }

    public function refreshToken(string $refreshToken): ?string
    {
        $token = $this->jiraClient->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken,
        ]);

        return $token->getToken();
    }
}
