<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\Api\Acl\Role\AclRoleCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ClientRuleEntity extends Entity
{
    use EntityIdTrait;

    protected string $clientId;
    protected bool $userBecomeAdmin;
    protected ?ClientEntity $client = null;
    protected ?ClientRuleConditionCollection $conditions = null;
    protected ?AclRoleCollection $aclRoles = null;

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function isUserBecomeAdmin(): bool
    {
        return $this->userBecomeAdmin;
    }

    public function getClient(): ?ClientEntity
    {
        return $this->client;
    }

    public function getConditions(): ?ClientRuleConditionCollection
    {
        return $this->conditions;
    }

    public function getAclRoles(): ?AclRoleCollection
    {
        return $this->aclRoles;
    }
}
