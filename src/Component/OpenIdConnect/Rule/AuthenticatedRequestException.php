<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect\Rule;

class AuthenticatedRequestException extends \Exception
{
    public function __construct(string $requestId, string $message, int $code)
    {
        parent::__construct(
            \sprintf('The request %s failed: %s', $requestId, $message),
            $code
        );
    }
}
