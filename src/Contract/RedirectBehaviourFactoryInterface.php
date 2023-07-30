<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;

interface RedirectBehaviourFactoryInterface
{
    /**
     * @throws LoadClientException
     */
    public function createRedirectBehaviour(string $clientId, Context $context): RedirectBehaviour;
}
