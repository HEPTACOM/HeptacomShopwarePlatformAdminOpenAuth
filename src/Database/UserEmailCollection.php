<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<UserEmailEntity>
 */
final class UserEmailCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return UserEmailEntity::class;
    }
}
