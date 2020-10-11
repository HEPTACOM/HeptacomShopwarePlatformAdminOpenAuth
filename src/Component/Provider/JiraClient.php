<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\OpenAuth\Atlassian;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use Mrjoops\OAuth2\Client\Provider\JiraResourceOwner;
use Psr\Http\Message\RequestInterface;

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
        $this->tokenPairFactory = $tokenPairFactory;
        $this->jiraClient = new Atlassian($options);
    }

    public function getLoginUrl(?string $state, RedirectBehaviour $behaviour): string
    {
        $behaviour = $behaviour ?? new RedirectBehaviour();
        $state = $state ?? '';
        $params = [];

        if ($state !== '') {
            $params[$behaviour->getStateKey()] = $state;
        }

        return $this->getInnerClient()->getAuthorizationUrl($params);
    }

    public function refreshToken(string $refreshToken): TokenPairStruct
    {
        return $this->tokenPairFactory->fromLeagueToken($this->getInnerClient()->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken,
        ]));
    }

    public function getUser(string $state, string $code, RedirectBehaviour $behaviour): UserStruct
    {
        $token = $this->getInnerClient()->getAccessToken('authorization_code', [$behaviour->getCodeKey() => $code]);
        /** @var JiraResourceOwner $user */
        $user = $this->getInnerClient()->getResourceOwner($token);

        return (new UserStruct())
            ->setPrimaryKey($user->getId())
            ->setTokenPair($this->tokenPairFactory->fromLeagueToken($token))
            ->setDisplayName($user->getName())
            ->setPrimaryEmail($user->getEmail())
            ->setEmails([])
            ->setPassthrough(['resourceOwner' => $user->toArray()]);
    }

    public function authorizeRequest(RequestInterface $request, TokenPairStruct $token): RequestInterface
    {
        $result = $request;

        foreach ($this->getInnerClient()->getHeaders($token->getAccessToken()) as $headerKey => $headerValue) {
            $result = $result->withAddedHeader($headerKey, $headerValue);
        }

        return $result;
    }

    public function getInnerClient(): Atlassian
    {
        return $this->jiraClient;
    }
}
