<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ClientRuleEntity>
 */
final class ClientRuleCollection extends EntityCollection
{
    public function filterByActionName(string $actionName): self
    {
        return $this->filterByProperty('actionName', $actionName);
    }

    protected function getExpectedClass(): string
    {
        return ClientRuleEntity::class;
    }
}
