<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Database\ClientRuleEntity;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

/**
 * This interface allows to define custom rule actions.
 * This way you can execute custom code based on rules when the user logs in.
 */
#[AutoconfigureTag('heptacom_open_auth.rule_action', attributes: ['default_index_method' => 'getName'])]
interface RuleActionInterface
{
    /**
     * Returns a unique name for the action (max. 64 characters).
     */
    public static function getName(): string;

    /**
     * Returns an admin component name that is used to configure the action.
     */
    public function getActionConfigurationComponent(): string;

    /**
     * Executes the action before resolving the SSO user into a shopware user, when the appropriate rule matches.
     */
    public function preResolveUser(ClientRuleEntity $rule, OAuthRuleScope $ruleScope): void;

    /**
     * Executes the action after the SSO user is resolved and the shopware user is updated, when the appropriate rule matches.
     * When this method is executed, the user is not yet logged in.
     */
    public function postResolveUser(ClientRuleEntity $rule, OAuthRuleScope $ruleScope, string $userId): void;
}
