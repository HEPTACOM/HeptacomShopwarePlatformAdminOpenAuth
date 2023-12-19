<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Saml\Rule;

use Heptacom\AdminOpenAuth\Component\Saml\Saml2UserData;
use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleContract;
use Shopware\Core\Framework\Rule\RuleComparison;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;

class Saml2RoleRule extends RuleContract
{
    public const RULE_NAME = 'heptacomAdminOpenAuthSaml2Role';

    public function __construct(
        protected string $operator = self::OPERATOR_EQ,
        protected ?array $roles = null
    ) {
        parent::__construct();
    }

    public function matchRule(OAuthRuleScope $scope): bool
    {
        $user = $scope->getUser();
        $userData = $user->getExtension(Saml2UserData::class);

        if (!$userData instanceof Saml2UserData) {
            return false;
        }

        $roles = \array_map(
            'mb_strtolower',
            \array_filter($userData->roles)
        );

        $expectedRoles = \array_map('mb_strtolower', $this->roles ?? []);

        foreach ($roles as $role) {
            if (RuleComparison::stringArray($role, $expectedRoles, $this->operator)) {
                return true;
            }
        }

        return false;
    }

    public function getConstraints(): array
    {
        return [
            'operator' => RuleConstraints::stringOperators(false),
            'roles' => RuleConstraints::stringArray(),
        ];
    }

    public function getConfig(): ?RuleConfig
    {
        return (new RuleConfig())
            ->operatorSet(RuleConfig::OPERATOR_SET_STRING, false, true)
            ->taggedField('roles');
    }
}
