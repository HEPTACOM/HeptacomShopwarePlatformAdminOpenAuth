<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<UserKeyEntity>
 */
final class UserKeyCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return UserKeyEntity::class;
    }
}
