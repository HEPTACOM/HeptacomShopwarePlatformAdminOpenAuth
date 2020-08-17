<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\OpenAuth\Atlassian;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Struct\UserStruct;
use Heptacom\OpenAuth\Token\Contract\TokenPairFactoryContract;
use League\OAuth2\Client\Provider\AbstractProvider;
use Mrjoops\OAuth2\Client\Provider\JiraResourceOwner;

class JiraClient extends ClientContract
{
    /**
     * @var TokenPairFactoryContract
     */
    private $tokenPairFactory;

    /**
     * @var Atlassian
     */
    private $jiraClient;

    public function __construct(TokenPairFactoryContract $tokenPairFactory, array $options)
    {
        parent::__construct($tokenPairFactory);
        $this->tokenPairFactory = $tokenPairFactory;
        $this->jiraClient = new Atlassian($options);
    }

    public function getUser(string $state, string $code, RedirectBehaviour $behaviour): UserStruct
    {
        $token = $this->jiraClient->getAccessToken('authorization_code', [$behaviour->getCodeKey() => $code]);
        /** @var JiraResourceOwner $user */
        $user = $this->jiraClient->getResourceOwner($token);

        return (new UserStruct())
            ->setPrimaryKey($user->getId())
            ->setTokenPair($this->tokenPairFactory->fromLeagueToken($token))
            ->setDisplayName($user->getName())
            ->setPrimaryEmail($user->getEmail())
            ->setEmails([])
            ->setPassthrough(['resourceOwner' => $user->toArray()]);
    }

    public function getInnerClient(): AbstractProvider
    {
        return $this->jiraClient;
    }
}
