<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\ClientProvider\Exception;

use Throwable;

abstract class ProvideClientException extends \RuntimeException
{
    /**
     * @var string
     */
    protected $providerClass;

    public function __construct(string $providerClass, ?Throwable $previous = null)
    {
        parent::__construct(\sprintf('%s can not provide a client', $providerClass), 0, $previous);
        $this->providerClass = $providerClass;
    }

    public function getProviderClass(): string
    {
        return $this->providerClass;
    }
}
