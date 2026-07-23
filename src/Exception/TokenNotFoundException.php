<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

class TokenNotFoundException extends \Exception
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('No token found for the requested authorization context.', $code, $previous);
    }
}
