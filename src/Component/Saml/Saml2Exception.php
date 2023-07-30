<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Saml;

class Saml2Exception extends \Exception
{
    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }
}
