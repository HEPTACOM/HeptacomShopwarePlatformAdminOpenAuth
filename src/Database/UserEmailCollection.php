<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                 add(UserEmailEntity $entity)
 * @method void                 set(string $key, UserEmailEntity $entity)
 * @method UserEmailEntity[]    getIterator()
 * @method UserEmailEntity[]    getElements()
 * @method UserEmailEntity|null get(string $key)
 * @method UserEmailEntity|null first()
 * @method UserEmailEntity|null last()
 */
class UserEmailCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return UserEmailEntity::class;
    }
}
