<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\Api\Acl\Role\AclRoleCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

final class ClientEntity extends Entity
{
    use EntityIdTrait;

    public ?string $name = null;

    public ?string $provider = null;

    public ?bool $active = null;

    public ?bool $login = null;

    public ?bool $connect = null;

    public ?bool $storeUserToken = null;

    public ?bool $userBecomeAdmin = null;

    public ?bool $keepUserUpdated = null;

    public ?array $config = null;

    public ?LoginCollection $logins = null;

    public ?UserEmailCollection $userEmails = null;

    public ?UserKeyCollection $userKeys = null;

    public ?UserTokenCollection $userTokens = null;

    public ?ClientRuleCollection $rules = null;

    public ?AclRoleCollection $defaultAclRoles = null;
}
