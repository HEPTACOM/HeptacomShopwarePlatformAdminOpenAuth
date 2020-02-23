<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

use Throwable;

class ProvideClientInvalidConfigurationException extends ProvideClientException
{
    public function __construct(string $clientId, string $providerClass, string $message, ?Throwable $previous = null)
    {
        parent::__construct($clientId, $providerClass, $previous);
        $this->message = $providerClass . ' can not provide a client for ' . $clientId . PHP_EOL . $message;
    }
}
