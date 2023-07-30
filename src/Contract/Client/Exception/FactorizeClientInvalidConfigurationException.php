<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\Client\Exception;

use Throwable;

final class FactorizeClientInvalidConfigurationException extends FactorizeClientException
{
    public function __construct(
        string $providerKey,
        public array $configuration,
        ?Throwable $previous = null
    ) {
        parent::__construct($providerKey, $previous);
    }
}
