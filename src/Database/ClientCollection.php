<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @extends EntityCollection<ClientEntity>
 */
final class ClientCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ClientEntity::class;
    }
}
