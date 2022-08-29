<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract\AuthorizationUrl;

use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Shopware\Core\Framework\Context;

interface LoginUrlGeneratorInterface
{
    public function generate(
        string $clientId,
        string $state,
        RedirectBehaviour $redirectBehaviour,
        Context $context
    ): string;
}
