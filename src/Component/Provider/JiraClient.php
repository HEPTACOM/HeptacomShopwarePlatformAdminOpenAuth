<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\OpenAuth\Atlassian;
use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\AdminOpenAuth\Contract\TokenPair;
use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Mrjoops\OAuth2\Client\Provider\JiraResourceOwner;
use Psr\Http\Message\RequestInterface;

final class JiraClient extends ClientContract
{
    public function __construct(
        private readonly TokenPairFactoryContract $tokenPairFactory,
        private readonly Atlassian $jiraClient,
    ) {
    }

    public function getLoginUrl(?string $state, RedirectBehaviour $behaviour): string
    {
        $state ??= '';
        $params = [];

        if (\is_string($behaviour->redirectUri)) {
            $params['redirect_uri'] = $behaviour->redirectUri;
        }

        if ($state !== '') {
            $params[$behaviour->stateKey] = $state;
        }

        return $this->getInnerClient()->getAuthorizationUrl($params);
    }

    public function refreshToken(string $refreshToken): TokenPair
    {
        return $this->tokenPairFactory->fromLeagueToken($this->getInnerClient()->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken,
        ]));
    }

    public function getUser(string $state, string $code, RedirectBehaviour $behaviour): User
    {
        $options = [$behaviour->codeKey => $code];

        if (\is_string($behaviour->redirectUri)) {
            $options['redirect_uri'] = $behaviour->redirectUri;
        }

        $token = $this->getInnerClient()->getAccessToken('authorization_code', $options);
        /** @var JiraResourceOwner $user */
        $user = $this->getInnerClient()->getResourceOwner($token);
        $fullUserData = $user->toArray();

        $result = new User();

        $result->primaryKey = $user->getId();
        $result->tokenPair = $this->tokenPairFactory->fromLeagueToken($token);
        $result->displayName = $user->getName();
        $result->primaryEmail = $user->getEmail();
        $result->timezone = $fullUserData['timezone'] ?? null;
        $result->addArrayExtension('resourceOwner', $user->toArray());

        return $result;
    }

    public function authorizeRequest(RequestInterface $request, TokenPair $token): RequestInterface
    {
        $result = $request;

        foreach ($this->getInnerClient()->getHeaders($token->accessToken) as $headerKey => $headerValue) {
            $result = $result->withAddedHeader($headerKey, $headerValue);
        }

        return $result;
    }

    public function getInnerClient(): Atlassian
    {
        return $this->jiraClient;
    }
}
