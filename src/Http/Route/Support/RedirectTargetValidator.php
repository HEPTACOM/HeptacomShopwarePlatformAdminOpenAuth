<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route\Support;

use Symfony\Component\HttpFoundation\Exception\BadRequestException;

final class RedirectTargetValidator
{
    private const SAME_ORIGIN_PATH = '{^/(?![/\\\\])[A-Za-z0-9._~!$&\'()*+,;=:@%/?#-]*$}';

    public static function assertSameOrigin(?string $redirectTo): ?string
    {
        if ($redirectTo === null || $redirectTo === '') {
            return null;
        }

        if (\preg_match(self::SAME_ORIGIN_PATH, $redirectTo) !== 1) {
            throw new BadRequestException('Only same-origin redirect paths are allowed.');
        }

        return $redirectTo;
    }
}
