<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Struct\UserStruct;
use Heptacom\OpenAuth\Token\Contract\TokenPairFactoryContract;
use League\OAuth2\Client\Provider\AbstractProvider;
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
        parent::__construct($tokenPairFactory);
        $this->tokenPairFactory = $tokenPairFactory;
        $this->azureClient = new Azure($options);
    }

    public function getUser(string $state, string $code, RedirectBehaviour $behaviour): UserStruct
    {
        $token = $this->azureClient->getAccessToken('authorization_code', [$behaviour->getCodeKey() => $code]);
        $user = $this->azureClient->get('me', $token);

        return (new UserStruct())
            ->setPrimaryKey($user['objectId'])
            ->setTokenPair($this->tokenPairFactory->fromLeagueToken($token))
            ->setDisplayName($user['displayName'])
            ->setPrimaryEmail($user['mail'])
            ->setEmails([])
            ->setPassthrough(['resourceOwner' => $user]);
    }

    public function getInnerClient(): AbstractProvider
    {
        return $this->azureClient;
    }
}
