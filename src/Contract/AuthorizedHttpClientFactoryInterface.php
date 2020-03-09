<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Component\Contract\AuthorizedHttpClientInterface;
use Shopware\Core\Framework\Context;

interface AuthorizedHttpClientFactoryInterface
{
    public function createAuthorizedHttpClient(
        string $clientId,
        string $userId,
        Context $context
    ): AuthorizedHttpClientInterface;
}
