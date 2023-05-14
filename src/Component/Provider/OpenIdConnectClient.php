<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectService;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\Http\Message\RequestInterface;

final class OpenIdConnectClient extends ClientContract
{
    private TokenPairFactoryContract $tokenPairFactory;

    private OpenIdConnectService $openIdConnectService;

    public function __construct(TokenPairFactoryContract $tokenPairFactory, OpenIdConnectService $openIdConnectService)
    {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->openIdConnectService = $openIdConnectService;
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
        return $this->tokenPairFactory->fromOpenIdConnectToken($this->getInnerClient()->getAccessToken('refresh_token', [
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
        $user = $this->getInnerClient()->getUserInfo($token);

        $name = $user->getName() ?? sprintf('%s %s', $user->getGivenName() ?? '', $user->getFamilyName() ?? '');
        if (empty(trim($name))) {
            $name = $user->getNickname() ?? $user->getPreferredUsername() ?? $user->getEmail();
        }

        return (new UserStruct())
            ->setPrimaryKey($user->getSub())
            ->setTokenPair($this->tokenPairFactory->fromOpenIdConnectToken($token))
            ->setFirstName($user->getGivenName() ?? '')
            ->setLastName($user->getFamilyName() ?? '')
            ->setDisplayName($name)
            ->setPrimaryEmail($user->getEmail())
            ->setEmails([$user->getEmail()])
            ->setLocale($user->getLocale() !== null ? str_replace('_', '-', $user->getLocale()) : null)
            ->setTimezone($user->getZoneinfo() ?? null)
            ->addPassthrough('picture', $user->getPicture());
    }

    public function authorizeRequest(RequestInterface $request, TokenPairStruct $token): RequestInterface
    {
        return $request->withAddedHeader('Authorization', 'Bearer ' . $token->getAccessToken());
    }

    public function getInnerClient(): OpenIdConnectService
    {
        return $this->openIdConnectService;
    }
}
