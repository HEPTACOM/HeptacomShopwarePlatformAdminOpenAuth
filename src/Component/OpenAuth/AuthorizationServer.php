<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenAuth;

use Heptacom\AdminOpenAuth\KskHeptacomAdminOpenAuth;
use League\OAuth2\Server\AuthorizationServer as LeagueAuthorizationServer;
use League\OAuth2\Server\Grant\GrantTypeInterface;
use League\OAuth2\Server\RequestTypes\AuthorizationRequestInterface;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
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

    #[\Override]
    public function enableGrantType(GrantTypeInterface $grantType, ?\DateInterval $accessTokenTTL = null): void
    {
        if ($this->systemConfigService->getBool(KskHeptacomAdminOpenAuth::CONFIG_DENY_PASSWORD_LOGIN) && $grantType->getIdentifier() === 'password') {
            return;
        }

        $this->decorated->enableGrantType($grantType, $accessTokenTTL);
    }

    #[\Override]
    public function validateAuthorizationRequest(ServerRequestInterface $request): AuthorizationRequestInterface
    {
        return $this->decorated->validateAuthorizationRequest($request);
    }

    #[\Override]
    public function completeAuthorizationRequest(AuthorizationRequestInterface $authRequest, ResponseInterface $response): ResponseInterface
    {
        return $this->decorated->completeAuthorizationRequest($authRequest, $response);
    }

    #[\Override]
    public function respondToAccessTokenRequest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        return $this->decorated->respondToAccessTokenRequest($request, $response);
    }

    #[\Override]
    public function setDefaultScope($defaultScope): void
    {
        $this->decorated->setDefaultScope($defaultScope);
    }

    public function revokeRefreshTokens(bool $revokeRefreshTokens): void
    {
        $this->decorated->revokeRefreshTokens($revokeRefreshTokens);
    }

    #[\Override]
    public function respondToDeviceAuthorizationRequest(
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface {
        return $this->decorated->respondToDeviceAuthorizationRequest($request, $response);
    }

    #[\Override]
    public function completeDeviceAuthorizationRequest(string $deviceCode, string $userId, bool $userApproved): void
    {
        $this->decorated->completeDeviceAuthorizationRequest($deviceCode, $userId, $userApproved);
    }

    #[\Override]
    protected function getResponseType(): ResponseTypeInterface
    {
        return $this->decorated->getResponseType();
    }
}
