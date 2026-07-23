<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

class TokenExpiredNoRefreshTokenException extends \Exception
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('Token is expired and no refresh token is available to obtain a new one.', $code, $previous);
    }
}
