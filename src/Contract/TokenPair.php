<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Struct\Struct;

final class TokenPair extends Struct
{
    public ?string $accessToken = null;

    public ?string $refreshToken = null;

    public ?\DateTimeInterface $expiresAt = null;
}
