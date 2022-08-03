<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Context;
use Shopware\Core\System\User\UserCollection;

interface UserKeyInterface
{
    public function add(string $userId, string $primaryKey, string $clientId, Context $context): string;

    public function searchUser(string $primaryKey, string $clientId, Context $context): UserCollection;
}
