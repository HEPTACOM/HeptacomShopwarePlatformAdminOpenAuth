<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\Route\Exception;

final class RedirectReceiveInvalidStateException extends RedirectReceiveException
{
    public function __construct(
        public string $state,
        ?\Throwable $previous = null
    ) {
        parent::__construct('No valid login found for the given state', 0, $previous);
    }
}
