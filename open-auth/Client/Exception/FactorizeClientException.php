<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Client\Exception;

use Throwable;

abstract class FactorizeClientException extends \RuntimeException
{
    /**
     * @var string
     */
    private $providerKey;

    public function __construct(string $providerKey, ?Throwable $previous = null)
    {
        parent::__construct(\sprintf('Unable to factorize client providing %s', $providerKey), 0, $previous);
        $this->providerKey = $providerKey;
    }

    public function getProviderKey(): string
    {
        return $this->providerKey;
    }
}
