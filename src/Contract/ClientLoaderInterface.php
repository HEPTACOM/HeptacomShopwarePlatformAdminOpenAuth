<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;

interface ClientLoaderInterface
{
    /**
     * @throws LoadClientException
     */
    public function load(string $clientId, Context $context): ClientInterface;
}
