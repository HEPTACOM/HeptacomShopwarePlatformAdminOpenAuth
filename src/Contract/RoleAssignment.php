<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Struct\Struct;

final class RoleAssignment extends Struct
{
    public bool $isAdministrator = false;

    public array $roleIds = [];
}
