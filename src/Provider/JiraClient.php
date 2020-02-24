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

    public function __construct(string $appId, string $appSecret, string $redirectUri)
    {
        $this->jiraClient = new Atlassian([
            'clientId' => $appId,
            'clientSecret' => $appSecret,
            'redirectUri' => $redirectUri,
        ]);
    }

    public function getLoginUrl(string $state): string
    {
        return $this->jiraClient->getAuthorizationUrl(['state' => $state]);
    }

    public function getUser(string $state, string $code): UserStruct
    {
        $token = $this->jiraClient->getAccessToken('authorization_code', [
            'code' => $code,
            'scope' => 'read:jira-user offline_access',
        ]);
        /** @var JiraResourceOwner $user */
        $user = $this->jiraClient->getResourceOwner($token);

        return (new UserStruct())
            ->setPrimaryKey($user->getId())
            ->setRefreshToken($token->getRefreshToken())
            ->setAccessToken($token->getToken())
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
