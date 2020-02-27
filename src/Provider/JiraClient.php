<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Provider;

use Heptacom\AdminOpenAuth\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Contract\TokenPairFactoryInterface;
use Heptacom\AdminOpenAuth\OpenAuth\Atlassian;
use Heptacom\AdminOpenAuth\Struct\TokenPairStruct;
use Heptacom\AdminOpenAuth\Struct\UserStruct;
use Mrjoops\OAuth2\Client\Provider\JiraResourceOwner;

class JiraClient implements ClientInterface
{
    /**
     * @var TokenPairFactoryInterface
     */
    private $tokenPairFactory;

    /**
     * @var Atlassian
     */
    private $jiraClient;

    /**
     * @var bool
     */
    private $storeToken;

    public function __construct(
        TokenPairFactoryInterface $tokenPairFactory,
        string $appId,
        string $appSecret,
        string $redirectUri,
        bool $storeToken
    ) {
        $this->tokenPairFactory = $tokenPairFactory;
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
            ->setTokenPair($this->storeToken ? $this->tokenPairFactory->fromLeagueToken($token) : null)
            ->setDisplayName($user->getName())
            ->setPrimaryEmail($user->getEmail())
            ->setEmails([]);
    }

    public function refreshToken(string $refreshToken): TokenPairStruct
    {
        return $this->tokenPairFactory->fromLeagueToken($this->jiraClient->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken,
        ]));
    }
}
