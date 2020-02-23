<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

use Exception;
use Throwable;

class ProvideClientException extends Exception
{
    /**
     * @var string
     */
    protected $clientId;

    /**
     * @var string
     */
    protected $providerClass;

    public function __construct(string $clientId, string $providerClass, ?Throwable $previous = null)
    {
        $message = $providerClass . ' can not provide a client for ' . $clientId;
        $this->clientId = $clientId;
        $this->providerClass = $providerClass;
        parent::__construct($message, 0, $previous);
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getProviderClass(): string
    {
        return $this->providerClass;
    }
}
