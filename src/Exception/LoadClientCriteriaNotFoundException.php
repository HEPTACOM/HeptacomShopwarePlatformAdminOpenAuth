<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

class LoadClientCriteriaNotFoundException extends LoadClientException
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('No client found to load by criteria', '', $code, $previous);
    }
}
