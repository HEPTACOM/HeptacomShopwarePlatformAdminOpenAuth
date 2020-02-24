<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Provider;

use Heptacom\AdminOpenAuth\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Struct\UserStruct;
use TheNetworg\OAuth2\Client\Provider\Azure;

class MicrosoftAzureClient implements ClientInterface
{
    /**
     * @var Azure
     */
    private $azureClient;

    /**
     * @var bool
     */
    private $storeToken;

    public function __construct(string $appId, string $appSecret, string $redirectUri, bool $storeToken)
    {
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
            ->setAccessToken($this->storeToken ? $token->getToken() : null)
            ->setRefreshToken($this->storeToken ? $token->getRefreshToken() : null)
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
