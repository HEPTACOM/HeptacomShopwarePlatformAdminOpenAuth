<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenAuth\Rule;

use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleContract;
use Shopware\Core\Framework\Rule\RuleComparison;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;

class LocaleRule extends RuleContract
{
    public const RULE_NAME = 'heptacomAdminOpenAuthLocale';

    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected ?array $locales = null
    ) {
        parent::__construct();
    }

    public function matchRule(OAuthRuleScope $scope): bool
    {
        $user = $scope->getUser();

        return RuleComparison::stringArray(
            \strtolower($user->locale ?? ''),
            \array_map('strtolower', $this->locales ?? []),
            $this->operator
        );
    }

    public function getConstraints(): array
    {
        return [
            'locales' => RuleConstraints::stringArray(),
            'operator' => RuleConstraints::stringOperators(false),
        ];
    }

    public function getConfig(): ?RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_STRING, false, true)
            ->taggedField('locales');
    }
}
