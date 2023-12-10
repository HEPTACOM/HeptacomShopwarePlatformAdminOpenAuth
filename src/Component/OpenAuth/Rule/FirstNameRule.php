<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenAuth\Rule;

use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleContract;
use Shopware\Core\Framework\Rule\RuleComparison;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;

class FirstNameRule extends RuleContract
{
    public const RULE_NAME = 'heptacomAdminOpenAuthFirstName';

    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected ?string $firstName = null
    ) {
        parent::__construct();
    }

    public function matchRule(OAuthRuleScope $scope): bool
    {
        $user = $scope->getUser();

        return RuleComparison::string($user->firstName, $this->firstName ?? '', $this->operator);
    }

    public function getConstraints(): array
    {
        $constraints = [
            'operator' => RuleConstraints::stringOperators(),
        ];

        if ($this->operator === self::OPERATOR_EMPTY) {
            return $constraints;
        }

        $constraints['firstName'] = RuleConstraints::string();

        return $constraints;
    }

    public function getConfig(): ?RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_STRING)
            ->stringField('firstName');
    }

}
