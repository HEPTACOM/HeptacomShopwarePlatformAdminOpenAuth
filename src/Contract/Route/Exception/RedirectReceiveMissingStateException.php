<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\Route\Exception;

use Heptacom\OpenAuth\Route\Exception\RedirectReceiveException;
use Throwable;

final class RedirectReceiveMissingStateException extends RedirectReceiveException
{
    public function __construct(
        public array $queryParams,
        public string $stateKey,
        ?Throwable $previous = null
    ) {
        parent::__construct('No state in request found', 0, $previous);
    }
}
