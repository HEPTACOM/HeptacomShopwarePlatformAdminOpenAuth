<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\ProviderInterface;
use Heptacom\AdminOpenAuth\Contract\ProviderRepositoryInterface;
use Traversable;

class ProviderRepository implements ProviderRepositoryInterface
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @param iterable|Traversable|ProviderInterface[] $providers
     */
    public function __construct(Traversable $providers)
    {
        $this->providers = \iterator_to_array($providers);
    }

    public function getProviders(): Traversable
    {
        yield from $this->providers;
    }

    public function getMatchingProviders(string $key): Traversable
    {
        foreach ($this->providers as $iterKey => $provider) {
            if ($provider->provides() === $key) {
                yield $iterKey => $provider;
            }
        }
    }

    public function getProviderKeys(): array
    {
        $keys = [];

        foreach ($this->providers as $iterKey => $provider) {
            $keys[] = $provider->provides();
        }

        return \array_values(\array_unique($keys));
    }
}
