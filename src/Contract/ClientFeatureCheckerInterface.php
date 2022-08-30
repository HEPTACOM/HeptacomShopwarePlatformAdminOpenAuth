<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Context;

interface ClientFeatureCheckerInterface
{
    public function canLogin(string $clientId, Context $context): bool;

    public function canConnect(string $clientId, Context $context): bool;

    public function canStoreUserTokens(string $clientId, Context $context): bool;

    public function canUsersBecomeAdmin(string $clientId, Context $context): bool;

    public function canKeepUserUpdated(string $clientId, Context $context): bool;
}
