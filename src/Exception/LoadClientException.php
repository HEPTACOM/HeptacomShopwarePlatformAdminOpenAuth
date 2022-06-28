<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

class LoadClientException extends \Exception
{
    protected string $clientId;

    public function __construct(string $message, string $clientId, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->clientId = $clientId;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }
}
