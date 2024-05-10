<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\OpenAuth;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Shopware\Core\Framework\Api\OAuth\Client\ApiClient;

final readonly class OneTimeTokenClientRepository implements ClientRepositoryInterface
{
    public function __construct(
        private ClientRepositoryInterface $decorated,
    ) {
    }

    public function getClientEntity($clientIdentifier)
    {
        if ($clientIdentifier === 'administration') {
            return new ApiClient('administration', true);
        }

        return $this->decorated->getClientEntity($clientIdentifier);
    }

    public function validateClient($clientIdentifier, $clientSecret, $grantType)
    {
        return $grantType === 'heptacom_admin_open_auth_one_time_token'
            || $this->decorated->validateClient($clientIdentifier, $clientSecret, $grantType);
    }
}
