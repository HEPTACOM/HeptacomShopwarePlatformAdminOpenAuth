<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Exception\ProvideClientException;
use Shopware\Core\Framework\Context;

interface ProviderInterface
{
    public function provides(): string;

    public function initializeClientConfiguration(string $clientId, Context $context): void;

    /**
     * @throws ProvideClientException
     */
    public function provideClient(string $clientId, array $config, Context $context): ClientInterface;
}
