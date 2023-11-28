<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

class LoadClientException extends \Exception
{
    public function __construct(
        string $message,
        public readonly string $clientId,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
