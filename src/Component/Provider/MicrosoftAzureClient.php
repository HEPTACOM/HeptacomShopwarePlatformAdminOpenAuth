<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Contract\TokenPairFactoryInterface;
use Heptacom\AdminOpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use League\OAuth2\Client\Provider\AbstractProvider;
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

    public function __construct(TokenPairFactoryInterface $tokenPairFactory, array $options)
    {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->azureClient = new Azure($options);
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
            ->setTokenPair($this->tokenPairFactory->fromLeagueToken($token))
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

    public function getInnerClient(): AbstractProvider
    {
        return $this->azureClient;
    }
}
