<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ClientRuleConditionEntity>
 */
final class ClientRuleConditionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ClientRuleConditionEntity::class;
    }
}
