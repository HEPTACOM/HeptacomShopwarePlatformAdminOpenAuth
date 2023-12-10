<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenAuth\Rule;

use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleContract;
use Shopware\Core\Framework\Rule\RuleComparison;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;

class PrimaryKeyRule extends RuleContract
{
    public const RULE_NAME = 'heptacomAdminOpenAuthPrimaryKey';

    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected ?array $primaryKeys = null
    ) {
        parent::__construct();
    }

    public function matchRule(OAuthRuleScope $scope): bool
    {
        $user = $scope->getUser();

        return RuleComparison::stringArray(
            $user->primaryKey,
            $this->primaryKeys ?? [],
            $this->operator
        );
    }

    public function getConstraints(): array
    {
        return [
            'operator' => RuleConstraints::stringOperators(false),
            'primaryKeys' => RuleConstraints::stringArray(),
        ];
    }

    public function getConfig(): ?RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_STRING, false, true)
            ->taggedField('primaryKeys');
    }

}
