<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\ClientProvider;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\ClientProvider\Exception\ProvideClientException;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class ClientProviderContract
{
    public function getConfigurationTemplate(): OptionsResolver
    {
        return new OptionsResolver();
    }

    public function getInitialConfiguration(): array
    {
        return [];
    }

    abstract public function provides(): string;

    /**
     * @throws ProvideClientException
     */
    abstract public function provideClient(array $resolvedConfig): ClientContract;
}
