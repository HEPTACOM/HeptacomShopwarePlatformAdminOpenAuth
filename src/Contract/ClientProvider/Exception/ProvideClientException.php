<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\ClientProvider\Exception;

use Throwable;

abstract class ProvideClientException extends \RuntimeException
{
    public function __construct(public string $providerClass, ?Throwable $previous = null)
    {
        parent::__construct(\sprintf('%s can not provide a client', $this->providerClass), 0, $previous);
    }
}
