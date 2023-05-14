<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<UserTokenEntity>
 */
final class UserTokenCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return UserTokenEntity::class;
    }
}
