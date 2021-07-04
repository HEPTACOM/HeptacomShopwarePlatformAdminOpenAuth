<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void             add(LoginEntity $entity)
 * @method void             set(string $key, LoginEntity $entity)
 * @method LoginEntity[]    getIterator()
 * @method LoginEntity[]    getElements()
 * @method LoginEntity|null get(string $key)
 * @method LoginEntity|null first()
 * @method LoginEntity|null last()
 */
class LoginCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return LoginEntity::class;
    }
}
