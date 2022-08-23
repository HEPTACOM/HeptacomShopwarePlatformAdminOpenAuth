<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\Http\Message\RequestInterface;
use TheNetworg\OAuth2\Client\Provider\Azure;

/**
 * @deprecated tag:v5.0.0 will be replaced by microsoft_azure_oidc provider
 */
class MicrosoftAzureClient extends ClientContract
{
    private TokenPairFactoryContract $tokenPairFactory;

    private Azure $azureClient;

    public function __construct(TokenPairFactoryContract $tokenPairFactory, array $options)
    {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->azureClient = new Azure($options);
    }

    public function getLoginUrl(?string $state, RedirectBehaviour $behaviour): string
    {
        $state = $state ?? '';
        $params = [];

        if (\is_string($behaviour->getRedirectUri())) {
            $params['redirect_uri'] = $behaviour->getRedirectUri();
        }

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
        $options = [$behaviour->getCodeKey() => $code];

        if (\is_string($behaviour->getRedirectUri())) {
            $options['redirect_uri'] = $behaviour->getRedirectUri();
        }

        $token = $this->getInnerClient()->getAccessToken('authorization_code', $options);
        $user = $this->getInnerClient()->get('me', $token);

        $emails = [];

        if (($email = \trim($user['mail'] ?? '')) !== '') {
            $emails[] = $email;
        }

        // TODO break fallback behaviour in v5 and make it configurable
        if (($email = \trim($user['userPrincipalName'] ?? '')) !== '') {
            $emails[] = $email;
        }

        return (new UserStruct())
            ->setPrimaryKey($user['objectId'])
            ->setTokenPair($this->tokenPairFactory->fromLeagueToken($token))
            ->setDisplayName($user['displayName'])
            ->setPrimaryEmail(\array_pop($emails))
            ->setEmails($emails)
            ->setPassthrough(['resourceOwner' => $user]);
    }

    public function authorizeRequest(RequestInterface $request, TokenPairStruct $token): RequestInterface
    {
        $result = $request;

        foreach ($this->getInnerClient()->getHeaders($token->getAccessToken()) as $headerKey => $headerValue) {
            $result = $result->withAddedHeader($headerKey, $headerValue);
        }

        return $result;
    }

    public function getInnerClient(): Azure
    {
        return $this->azureClient;
    }
}
