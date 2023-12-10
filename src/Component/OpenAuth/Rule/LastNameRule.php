<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenAuth\Rule;

use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleContract;
use Shopware\Core\Framework\Rule\RuleComparison;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;

class LastNameRule extends RuleContract
{
    public const RULE_NAME = 'heptacomAdminOpenAuthLastName';

    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected ?string $lastName = null
    ) {
        parent::__construct();
    }

    public function matchRule(OAuthRuleScope $scope): bool
    {
        $user = $scope->getUser();

        return RuleComparison::string($user->lastName, $this->lastName ?? '', $this->operator);
    }

    public function getConstraints(): array
    {
        $constraints = [
            'operator' => RuleConstraints::stringOperators(),
        ];

        if ($this->operator === self::OPERATOR_EMPTY) {
            return $constraints;
        }

        $constraints['lastName'] = RuleConstraints::string();

        return $constraints;
    }

    public function getConfig(): ?RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_STRING)
            ->stringField('lastName');
    }

}
