<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectService;
use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\AdminOpenAuth\Contract\TokenPair;
use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Psr\Http\Message\RequestInterface;

final class OpenIdConnectClient extends ClientContract
{
    public function __construct(
        private readonly TokenPairFactoryContract $tokenPairFactory,
        private readonly OpenIdConnectService $openIdConnectService
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
        return $this->tokenPairFactory->fromOpenIdConnectToken($this->getInnerClient()->getAccessToken('refresh_token', [
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
        $user = $this->getInnerClient()->getUserInfo($token);

        $name = $user->getName() ?? \sprintf('%s %s', $user->getGivenName() ?? '', $user->getFamilyName() ?? '');
        if (empty(\trim($name))) {
            $name = $user->getNickname() ?? $user->getPreferredUsername() ?? $user->getEmail();
        }

        $result = new User();
        $result->primaryKey = $user->getSub();
        $result->tokenPair = $this->tokenPairFactory->fromOpenIdConnectToken($token);
        $result->firstName = $user->getGivenName() ?? '';
        $result->lastName = $user->getFamilyName() ?? '';
        $result->displayName = $name;
        $result->primaryEmail = $user->getEmail();
        $result->emails = [$user->getEmail()];
        $result->timezone = $user->getZoneinfo();

        if ($user->getLocale() !== null) {
            $result->locale = \str_replace('_', '-', $user->getLocale());
        }

        $result->addArrayExtension('picture', [
            'picture' => $user->getPicture(),
        ]);

        return $result;
    }

    public function authorizeRequest(RequestInterface $request, TokenPair $token): RequestInterface
    {
        return $request->withAddedHeader('Authorization', 'Bearer ' . $token->accessToken);
    }

    public function getInnerClient(): OpenIdConnectService
    {
        return $this->openIdConnectService;
    }
}
