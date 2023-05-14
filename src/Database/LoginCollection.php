<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<LoginEntity>
 */
final class LoginCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return LoginEntity::class;
    }
}
