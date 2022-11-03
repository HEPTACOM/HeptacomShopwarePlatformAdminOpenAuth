<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;

/**
 * If implemented, the client is allowed to modify the created RedirectBehaviour
 */
interface ModifiedRedirectBehaviourClientContract
{
    /**
     * Modifies the redirect behaviour after creation
     */
    public function modifyRedirectBehaviour(RedirectBehaviour $behaviour): void;
}
