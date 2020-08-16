<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Client\Exception;

use Throwable;

class FactorizeClientInvalidConfigurationException extends FactorizeClientException
{
    private $configuration;

    public function __construct(string $providerKey, array $configuration, ?Throwable $previous = null)
    {
        parent::__construct($providerKey, $previous);
        $this->configuration = $configuration;
    }

    public function getConfiguration(): array
    {
        return $this->configuration;
    }
}
