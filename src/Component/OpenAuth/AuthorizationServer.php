<?php

declare(strict_types=1);


namespace Heptacom\AdminOpenAuth\Component\OpenAuth;

use DateInterval;
use Heptacom\AdminOpenAuth\KskHeptacomAdminOpenAuth;
use League\OAuth2\Server\AuthorizationServer as LeagueAuthorizationServer;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class AuthorizationServer extends LeagueAuthorizationServer
{
    private LeagueAuthorizationServer $decorated;

    private SystemConfigService $systemConfigService;

    public function setDecorated(LeagueAuthorizationServer $decorated): void
    {
        $this->decorated = $decorated;
    }

    public function setSystemConfigService(SystemConfigService $systemConfigService): void
    {
        $this->systemConfigService = $systemConfigService;
    }

    public function enableGrantType(GrantTypeInterface $grantType, DateInterval $accessTokenTTL = null)
    {
        if ($this->systemConfigService->getBool(KskHeptacomAdminOpenAuth::CONFIG_DENY_PASSWORD_LOGIN) && $grantType->getIdentifier() === 'password') {
            return;
        }

        $this->decorated->enableGrantType($grantType, $accessTokenTTL);
    }

    public function validateAuthorizationRequest(ServerRequestInterface $request)
    {
        return $this->decorated->validateAuthorizationRequest($request);
    }

    public function completeAuthorizationRequest(AuthorizationRequest $authRequest, ResponseInterface $response)
    {
        return $this->decorated->completeAuthorizationRequest($authRequest, $response);
    }

    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseInterface $response)
    {
        return $this->decorated->respondToAccessTokenRequest($request, $response);
    }

    protected function getResponseType()
    {
        return $this->decorated->getResponseType();
    }

    public function setDefaultScope($defaultScope)
    {
        $this->decorated->setDefaultScope($defaultScope);
    }

    public function revokeRefreshTokens(bool $revokeRefreshTokens): void
    {
        $this->decorated->revokeRefreshTokens($revokeRefreshTokens);
    }
}
