<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Client\Contract;

use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use Heptacom\OpenAuth\Token\Contract\TokenPairFactoryContract;
use League\OAuth2\Client\Provider\AbstractProvider;

abstract class ClientContract
{
    /**
     * @var TokenPairFactoryContract
     */
    private $tokenPairFactory;

    public function __construct(TokenPairFactoryContract $tokenPairFactory)
    {
        $this->tokenPairFactory = $tokenPairFactory;
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

    abstract public function getUser(string $state, string $code, RedirectBehaviour $behaviour): UserStruct;

    abstract public function getInnerClient(): AbstractProvider;
}
