<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

class LoadClientException extends \Exception
{
    public function __construct(string $message, protected string $clientId, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }
}
