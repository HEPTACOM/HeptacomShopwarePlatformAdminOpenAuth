<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Exception\ProvideClientException;
use Shopware\Core\Framework\Context;

interface ProviderInterface
{
    public function provides(): string;

    /**
     * @throws ProvideClientException
     */
    public function provideClient(string $clientId, array $config, Context $context): ClientInterface;
}