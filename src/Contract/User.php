<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Shopware\Core\Framework\Struct\Struct;

final class User extends Struct
{
    public string $primaryEmail = '';

    /**
     * @var array|string[]
     */
    public array $emails = [];

    public string $firstName = '';

    public string $lastName = '';

    public string $displayName = '';

    public ?string $timezone = null;

    public ?string $locale = null;

    public string $primaryKey = '';

    public ?TokenPairStruct $tokenPair = null;
}
