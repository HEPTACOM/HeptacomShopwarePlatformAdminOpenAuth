<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect\Rule;

use Shopware\Core\Framework\Rule\RuleComparison;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;

class MicrosoftEntraIdOidcGroupsRule extends AuthenticatedODataRequestRule
{
    public const RULE_NAME = 'heptacomAdminOpenAuthMicrosoftEntraIdOidcGroups';

    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected ?array $groupIds = null
    ) {
        parent::__construct(
            'https://graph.microsoft.com/v1.0/me/transitiveMemberOf',
            'value[*].id'
        );
    }

    public function getConstraints(): array
    {
        return [
            'operator' => RuleConstraints::stringOperators(false),
            'groupIds' => RuleConstraints::stringArray(),
        ];
    }

    public function getConfig(): ?RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_STRING, false, true)
            ->taggedField('groupIds');
    }

    protected function validateExpressionResult($evaluatedExpression): bool
    {
        if ($this->groupIds === null) {
            return false;
        }

        $groupIds = \array_map('mb_strtolower', $evaluatedExpression);

        $expectedGroupIds = \array_map('mb_strtolower', $this->groupIds);

        foreach ($groupIds as $groupId) {
            if (RuleComparison::stringArray($groupId, $expectedGroupIds, $this->operator)) {
                return true;
            }
        }

        return false;
    }
}
