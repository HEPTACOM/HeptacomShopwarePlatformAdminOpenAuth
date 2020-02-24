<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

interface ClientInterface
{
    public function getLoginUrl(string $state): string;

    public function getUser(string $state, string $code): UserStruct;

    public function refreshToken(string $refreshToken): ?string;
}
