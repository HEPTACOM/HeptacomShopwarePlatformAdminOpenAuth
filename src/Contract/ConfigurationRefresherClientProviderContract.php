<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

/**
 * If implemented, the client provider can update its configuration if necessary.
 */
interface ConfigurationRefresherClientProviderContract
{
    /**
     * Checks if the configuration needs to be updated.
     */
    public function configurationNeedsUpdate(array $configuration): bool;

    /**
     * Refreshes the client configuration.
     */
    public function refreshConfiguration(array $configuration): array;
}
