<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\OpenAuth;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Shopware\Core\Framework\Api\OAuth\Client\ApiClient;

class OneTimeTokenClientRepository implements ClientRepositoryInterface
{
    /**
     * @var ClientRepositoryInterface
     */
    private $decorated;

    public function __construct(ClientRepositoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function getClientEntity(
        $clientIdentifier,
        $grantType = null,
        $clientSecret = null,
        $mustValidateSecret = true
    ) {
        if ($grantType === 'heptacom_admin_open_auth_one_time_token' && $clientIdentifier === 'administration') {
            return new ApiClient('administration', true);
        }

        return $this->decorated->getClientEntity($clientIdentifier, $grantType, $clientSecret, $mustValidateSecret);
    }
}
