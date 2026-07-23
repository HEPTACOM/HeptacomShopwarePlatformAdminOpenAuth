<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

class ClientFeatureNotSupportedException extends \Exception
{
    /**
     * @param class-string $clientType
     * @param class-string $feature
     */
    public function __construct(
        public readonly string $clientType,
        public readonly string $feature,
        int $code = 0,
    ) {
        parent::__construct(
            \sprintf(
                'The client "%s" does not support "%s"',
                $clientType,
                $feature,
            ),
            $code
        );
    }
}
