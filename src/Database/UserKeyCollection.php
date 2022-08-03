<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void               add(UserKeyEntity $entity)
 * @method void               set(string $key, UserKeyEntity $entity)
 * @method UserKeyEntity[]    getIterator()
 * @method UserKeyEntity[]    getElements()
 * @method UserKeyEntity|null get(string $key)
 * @method UserKeyEntity|null first()
 * @method UserKeyEntity|null last()
 */
class UserKeyCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return UserKeyEntity::class;
    }
}
