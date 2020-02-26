<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Provider;

use Heptacom\AdminOpenAuth\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Contract\TokenPairFactoryInterface;
use Heptacom\AdminOpenAuth\Struct\TokenPairStruct;
use Heptacom\AdminOpenAuth\Struct\UserStruct;
use TheNetworg\OAuth2\Client\Provider\Azure;

class MicrosoftAzureClient implements ClientInterface
{
    /**
     * @var TokenPairFactoryInterface
     */
    private $tokenPairFactory;

    /**
     * @var Azure
     */
    private $azureClient;

    /**
     * @var bool
     */
    private $storeToken;

    public function __construct(TokenPairFactoryInterface $tokenPairFactory, string $appId, string $appSecret, string $redirectUri, bool $storeToken)
    {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->azureClient = new Azure([
            'clientId' => $appId,
            'clientSecret' => $appSecret,
            'redirectUri' => $redirectUri,
        ]);
        $this->storeToken = $storeToken;
    }

    public function getLoginUrl(string $state): string
    {
        return $this->azureClient->getAuthorizationUrl(['state' => $state]);
    }

    public function getUser(string $state, string $code): UserStruct
    {
        $token = $this->azureClient->getAccessToken('authorization_code', ['code' => $code]);
        $user = $this->azureClient->get('me', $token);

        return (new UserStruct())
            ->setPrimaryKey($user['objectId'])
            ->setTokenPair($this->storeToken ? $this->tokenPairFactory->fromLeagueToken($token) : null)
            ->setDisplayName($user['displayName'])
            ->setPrimaryEmail($user['mail'])
            ->setEmails([]);
    }

    public function refreshToken(string $refreshToken): TokenPairStruct
    {
        return $this->tokenPairFactory->fromLeagueToken($this->azureClient->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken,
        ]));
    }
}
