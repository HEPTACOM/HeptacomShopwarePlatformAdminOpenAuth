<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\Client\Exception;

use Throwable;

abstract class FactorizeClientException extends \RuntimeException
{
    public function __construct(
        public string $providerKey,
        ?Throwable $previous = null
    ) {
        parent::__construct(\sprintf('Unable to factorize client providing %s', $this->providerKey), 0, $previous);
    }
}
