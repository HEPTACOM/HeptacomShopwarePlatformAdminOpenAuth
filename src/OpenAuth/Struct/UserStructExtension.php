<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\OpenAuth\Struct;

use Shopware\Core\Framework\Struct\Struct;

class UserStructExtension
{
    private bool $isAdmin = false;

    /**
     * @var string[]
     */
    private array $aclRoleIds = [];

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    public function getAclRoleIds(): array
    {
        return $this->aclRoleIds;
    }

    public function setAclRoleIds(array $aclRoleIds): void
    {
        $this->aclRoleIds = $aclRoleIds;
    }
}
