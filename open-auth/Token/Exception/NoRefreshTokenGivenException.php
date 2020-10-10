<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Token\Exception;

use Throwable;

class NoRefreshTokenGivenException extends UnrefreshableException
{
    public function __construct(?Throwable $previous = null)
    {
        parent::__construct('No refresh token given to refresh', 0, $previous);
    }
}
