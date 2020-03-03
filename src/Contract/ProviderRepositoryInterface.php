<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Traversable;

interface ProviderRepositoryInterface
{
    /**
     * @return Traversable|ProviderInterface[]
     */
    public function getProviders(): Traversable;

    /**
     * @return Traversable|ProviderInterface[]
     */
    public function getMatchingProviders(string $key): Traversable;

    /**
     * @return array|string[]
     */
    public function getProviderKeys(): array;
}
