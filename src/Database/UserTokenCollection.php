<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                 add(UserTokenEntity $entity)
 * @method void                 set(string $key, UserTokenEntity $entity)
 * @method UserTokenEntity[]    getIterator()
 * @method UserTokenEntity[]    getElements()
 * @method UserTokenEntity|null get(string $key)
 * @method UserTokenEntity|null first()
 * @method UserTokenEntity|null last()
 */
class UserTokenCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return UserTokenEntity::class;
    }
}
