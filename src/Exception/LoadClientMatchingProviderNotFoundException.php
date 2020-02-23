<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

use Throwable;

class LoadClientMatchingProviderNotFoundException extends LoadClientException
{
    public function __construct(string $clientId, ?Throwable $previous = null)
    {
        $message = 'No provider that can provide client by id ' . $clientId . ' found';
        parent::__construct($message, $clientId, $previous);
    }
}
