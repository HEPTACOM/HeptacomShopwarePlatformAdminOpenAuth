<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ClientRuleEntity extends Entity
{
    use EntityIdTrait;

    protected string $clientId;

    protected string $actionName;

    protected array $actionConfig;

    protected bool $stopOnMatch;

    protected ?ClientEntity $client = null;

    protected ?ClientRuleConditionCollection $conditions = null;

    protected int $position;

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function isStopOnMatch(): bool
    {
        return $this->stopOnMatch;
    }

    public function getClient(): ?ClientEntity
    {
        return $this->client;
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }

    public function getActionConfig(): array
    {
        return $this->actionConfig;
    }

    public function getConditions(): ?ClientRuleConditionCollection
    {
        return $this->conditions;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
