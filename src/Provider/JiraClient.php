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

    public function __construct(TokenPairFactoryInterface $tokenPairFactory, array $options)
    {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->jiraClient = new Atlassian($options);
    }

    public function getLoginUrl(string $state): string
    {
        return $this->jiraClient->getAuthorizationUrl(['state' => $state]);
    }

    public function getUser(string $state, string $code): UserStruct
    {
        $token = $this->jiraClient->getAccessToken('authorization_code', ['code' => $code]);
        /** @var JiraResourceOwner $user */
        $user = $this->jiraClient->getResourceOwner($token);

        return (new UserStruct())
            ->setPrimaryKey($user->getId())
            ->setTokenPair($this->tokenPairFactory->fromLeagueToken($token))
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
