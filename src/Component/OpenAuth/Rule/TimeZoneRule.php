<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenAuth\Rule;

use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleContract;
use Shopware\Core\Framework\Rule\RuleComparison;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;

class TimeZoneRule extends RuleContract
{
    public const RULE_NAME = 'heptacomAdminOpenAuthTimeZone';

    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected ?array $timeZones = null
    ) {
        parent::__construct();
    }

    public function matchRule(OAuthRuleScope $scope): bool
    {
        $user = $scope->getUser();

        return RuleComparison::stringArray(
            \strtolower($user->timezone ?? ''),
            \array_map('strtolower', $this->timeZones ?? []),
            $this->operator
        );
    }

    public function getConstraints(): array
    {
        return [
            'operator' => RuleConstraints::stringOperators(false),
            'timeZones' => RuleConstraints::stringArray(),
        ];
    }

    public function getConfig(): ?RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_STRING, false, true)
            ->taggedField('timeZones');
    }

}
