<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Provider;

use Heptacom\AdminOpenAuth\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Contract\UserStruct;
use TheNetworg\OAuth2\Client\Provider\Azure;

class MicrosoftAzureClient implements ClientInterface
{
    /**
     * @var Azure
     */
    private $azureClient;

    public function __construct(string $appId, string $appSecret, string $redirectUri)
    {
        $this->azureClient = new Azure([
            'clientId' => $appId,
            'clientSecret' => $appSecret,
            'redirectUri' => $redirectUri,
        ]);
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
            ->setAccessToken($token->getToken())
            ->setRefreshToken($token->getRefreshToken())
            ->setDisplayName($user['displayName'])
            ->setPrimaryEmail($user['mail'])
            ->setEmails([]);
    }

    public function refreshToken(string $refreshToken): ?string
    {
        $token = $this->azureClient->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken,
        ]);

        return $token->getToken();
    }
}
