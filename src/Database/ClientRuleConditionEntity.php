<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ClientRuleConditionEntity extends Entity
{
    use EntityIdTrait;

    protected string $type;

    protected string $clientRuleId;

    protected ?ClientRuleDefinition $clientRule = null;

    protected ?string $parentId = null;

    protected ?ClientRuleConditionEntity $parent = null;

    protected ?ClientRuleConditionCollection $children = null;

    protected ?array $value = null;

    protected int $position;

    public function getType(): string
    {
        return $this->type;
    }

    public function getClientRuleId(): string
    {
        return $this->clientRuleId;
    }

    public function getClientRule(): ?ClientRuleDefinition
    {
        return $this->clientRule;
    }

    public function getParentId(): ?string
    {
        return $this->parentId;
    }

    public function getParent(): ?ClientRuleConditionEntity
    {
        return $this->parent;
    }

    public function getChildren(): ?ClientRuleConditionCollection
    {
        return $this->children;
    }

    public function getValue(): ?array
    {
        return $this->value;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
