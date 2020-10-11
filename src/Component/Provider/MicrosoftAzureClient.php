<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\Http\Message\RequestInterface;
use TheNetworg\OAuth2\Client\Provider\Azure;

class MicrosoftAzureClient extends ClientContract
{
    /**
     * @var TokenPairFactoryContract
     */
    private $tokenPairFactory;

    /**
     * @var Azure
     */
    private $azureClient;

    public function __construct(TokenPairFactoryContract $tokenPairFactory, array $options)
    {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->azureClient = new Azure($options);
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
        $user = $this->getInnerClient()->get('me', $token);

        return (new UserStruct())
            ->setPrimaryKey($user['objectId'])
            ->setTokenPair($this->tokenPairFactory->fromLeagueToken($token))
            ->setDisplayName($user['displayName'])
            ->setPrimaryEmail($user['mail'])
            ->setEmails([])
            ->setPassthrough(['resourceOwner' => $user]);
    }

    public function authorizeRequest(RequestInterface $request, TokenPairStruct $token)
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
