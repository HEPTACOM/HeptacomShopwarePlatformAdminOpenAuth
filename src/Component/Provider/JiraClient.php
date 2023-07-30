<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\OpenAuth\Atlassian;
use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use Mrjoops\OAuth2\Client\Provider\JiraResourceOwner;
use Psr\Http\Message\RequestInterface;

final class JiraClient extends ClientContract
{
    private TokenPairFactoryContract $tokenPairFactory;

    private Atlassian $jiraClient;

    public function __construct(TokenPairFactoryContract $tokenPairFactory, array $options)
    {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->jiraClient = new Atlassian($options);
    }

    public function getLoginUrl(?string $state, RedirectBehaviour $behaviour): string
    {
        $state = $state ?? '';
        $params = [];

        if (\is_string($behaviour->redirectUri)) {
            $params['redirect_uri'] = $behaviour->redirectUri;
        }

        if ($state !== '') {
            $params[$behaviour->stateKey] = $state;
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
        $options = [$behaviour->codeKey => $code];

        if (\is_string($behaviour->redirectUri)) {
            $options['redirect_uri'] = $behaviour->redirectUri;
        }

        $token = $this->getInnerClient()->getAccessToken('authorization_code', $options);
        /** @var JiraResourceOwner $user */
        $user = $this->getInnerClient()->getResourceOwner($token);
        $fullUserData = $user->toArray();

        return (new UserStruct())
            ->setPrimaryKey($user->getId())
            ->setTokenPair($this->tokenPairFactory->fromLeagueToken($token))
            ->setDisplayName($user->getName())
            ->setPrimaryEmail($user->getEmail())
            ->setEmails([])
            ->setTimezone($fullUserData['timezone'] ?? null)
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
