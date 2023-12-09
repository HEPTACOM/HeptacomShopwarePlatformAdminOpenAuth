<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\RuleScope;

abstract class RuleContract extends Rule
{
    public function match(RuleScope $scope): bool
    {
        if (!$scope instanceof OAuthRuleScope) {
            return false;
        }

        return $this->matchRule($scope);
    }

    abstract public function matchRule(OAuthRuleScope $scope): bool;

    public function getApiAlias(): string
    {
        return 'heptacom_admin_open_auth_rule_' . $this->getName();
    }
}
