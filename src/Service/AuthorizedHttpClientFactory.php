<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Component\AuthorizedHttpClient;
use Heptacom\AdminOpenAuth\Component\Contract\AuthorizedHttpClientInterface;
use Heptacom\AdminOpenAuth\Contract\AuthorizedHttpClientFactoryInterface;
use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\TokenRefresherInterface;
use Shopware\Core\Framework\Context;

class AuthorizedHttpClientFactory implements AuthorizedHttpClientFactoryInterface
{
    /**
     * @var ClientLoaderInterface
     */
    private $clientLoader;

    /**
     * @var TokenRefresherInterface
     */
    private $tokenRefresher;

    public function __construct(ClientLoaderInterface $clientLoader, TokenRefresherInterface $tokenRefresher)
    {
        $this->clientLoader = $clientLoader;
        $this->tokenRefresher = $tokenRefresher;
    }

    public function createAuthorizedHttpClient(
        string $clientId,
        string $userId,
        Context $context
    ): AuthorizedHttpClientInterface {
        $provider = $this->clientLoader->load($clientId, $context);

        return new AuthorizedHttpClient(
            $provider->getInnerClient(),
            $this->tokenRefresher,
            $context,
            $clientId,
            $userId,
            15
        );
    }
}
