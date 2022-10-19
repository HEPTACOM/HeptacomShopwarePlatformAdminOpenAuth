<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderService;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\Http\Message\RequestInterface;

class Saml2ServiceProviderClient extends ClientContract
{
    // TODO: SAML: implement

    private TokenPairFactoryContract $tokenPairFactory;

    private Saml2ServiceProviderService $saml2ServiceProviderService;

    public function __construct(TokenPairFactoryContract $tokenPairFactory, Saml2ServiceProviderService $saml2ServiceProviderService)
    {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->saml2ServiceProviderService = $saml2ServiceProviderService;
    }

    public function getLoginUrl(?string $state, RedirectBehaviour $behaviour): string
    {
        $state = $state ?? '';
        $params = [];

        $behaviour->setStateKey('RelayState');

        if (\is_string($behaviour->getRedirectUri())) {
            $params['SamlRequest'] = $this->getInnerClient()
                ->buildSamlRequest($behaviour->getRedirectUri());
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


        return (new UserStruct()); // TODO: SAML: Build user struct
    }

    public function authorizeRequest(RequestInterface $request, TokenPairStruct $token): RequestInterface
    {
        // TODO: SAML: check if support is possible
        throw new \RuntimeException('Not supported');

        return $request;
    }

    public function getInnerClient(): Saml2ServiceProviderService
    {
        return $this->saml2ServiceProviderService;
    }
}
