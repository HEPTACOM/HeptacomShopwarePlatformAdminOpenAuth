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
     * @return string
     */
    public static function getName(): string;

    /**
     * Returns an admin component name that is used to configure the action.
     * @return string
     */
    public function getActionConfigurationComponent(): string;

    /**
     * Executes the action when the appropriate rule matches.
     * @param ClientRuleEntity $rule
     * @param OAuthRuleScope $ruleScope
     * @return void
     */
    public function execute(ClientRuleEntity $rule, OAuthRuleScope $ruleScope): void;
}
