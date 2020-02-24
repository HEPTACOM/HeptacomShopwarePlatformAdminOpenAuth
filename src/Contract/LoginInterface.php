<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Database\LoginEntity;
use Shopware\Core\Framework\Context;

interface LoginInterface
{
    public function initiate(string $clientId, string $state, Context $context): string;

    public function setCredentials(string $state, string $userId, string $password, Context $context): bool;

    public function pop(string $state, Context $context): ?LoginEntity;
}
