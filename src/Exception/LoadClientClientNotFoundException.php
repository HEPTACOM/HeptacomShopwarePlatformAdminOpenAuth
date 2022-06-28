<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

class LoadClientClientNotFoundException extends LoadClientException
{
    public function __construct(string $clientId, ?\Throwable $previous = null)
    {
        parent::__construct('No client found to load by id ' . $clientId, $clientId, $previous);
    }
}
