<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenAuth\Rule;

use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleContract;
use Shopware\Core\Framework\Rule\RuleComparison;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;

class EmailRule extends RuleContract
{
    public const RULE_NAME = 'heptacomAdminOpenAuthEmail';

    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected ?array $emails = null
    ) {
        parent::__construct();
    }

    public function matchRule(OAuthRuleScope $scope): bool
    {
        $user = $scope->getUser();

        $emails = \array_map(
            'strtolower',
            \array_filter([
                $user->primaryEmail,
                ...$user->emails,
            ])
        );

        $allowedEmails = \array_map('strtolower', $this->emails ?? []);

        foreach ($emails as $email) {
            if (RuleComparison::stringArray($email, $allowedEmails, $this->operator)) {
                return true;
            }
        }

        return false;
    }

    public function getConstraints(): array
    {
        return [
            'emails' => RuleConstraints::stringArray(),
            'operator' => RuleConstraints::stringOperators(false),
        ];
    }

    public function getConfig(): ?RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_STRING, false, true)
            ->taggedField('emails');
    }
}
