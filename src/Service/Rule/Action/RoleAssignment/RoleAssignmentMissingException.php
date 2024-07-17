<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Rule\Action\RoleAssignment;

class RoleAssignmentMissingException extends \Exception
{
    public function __construct()
    {
        parent::__construct('RoleAssignmentAction requires a RoleAssignment extension on the user. The user might not have the correct roles.');
    }
}
