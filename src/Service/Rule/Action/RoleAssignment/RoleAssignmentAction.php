<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Rule\Action\RoleAssignment;

use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RoleAssignment;
use Heptacom\AdminOpenAuth\Contract\RuleActionInterface;
use Heptacom\AdminOpenAuth\Database\ClientRuleEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\DependencyInjection\Attribute\AsTaggedItem;

#[AsTaggedItem('heptacom_open_auth.rule_action', priority: 1000)]
class RoleAssignmentAction implements RuleActionInterface
{
    public function __construct(
        private readonly EntityRepository $aclRoleRepository,
    ) {
    }

    public static function getName(): string
    {
        return 'heptacomAdminOpenAuthRoleAssignment';
    }

    public function getActionConfigurationComponent(): string
    {
        return 'heptacom-admin-open-auth-role-assignment-action-config';
    }

    public function execute(ClientRuleEntity $rule, OAuthRuleScope $ruleScope): void
    {
        $actionConfig = $rule->getActionConfig();

        $roleAssignment = $ruleScope->getUser()
            ->getExtensionOfType('roleAssignment', RoleAssignment::class);

        if (!$roleAssignment instanceof RoleAssignment) {
            // TODO throw exception or log error; however this should never happen
            return;
        }

        if (!$roleAssignment->isAdministrator) {
            $roleAssignment->isAdministrator = $actionConfig['userBecomeAdmin'] ?? false;
        }

        $configuredRoleIds = $actionConfig['aclRoleIds'] ?? [];

        if ($configuredRoleIds !== []) {
            $roleIds = $this->aclRoleRepository->searchIds(
                new Criteria($configuredRoleIds),
                $ruleScope->getContext()
            )->getIds();

            $roleAssignment->roleIds = [
                ...$roleAssignment->roleIds,
                ...$roleIds,
            ];
        }
    }

}
