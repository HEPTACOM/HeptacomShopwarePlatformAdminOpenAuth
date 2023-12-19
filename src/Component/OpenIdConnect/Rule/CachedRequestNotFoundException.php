<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect\Rule;

class CachedRequestNotFoundException extends \Exception
{
    public function __construct(string $requestId)
    {
        parent::__construct(
            \sprintf('The request %s is not cached.', $requestId),
            1702992544
        );
    }
}
