<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Rule;

use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleActionInterface;
use Heptacom\AdminOpenAuth\Database\ClientRuleCollection;
use Heptacom\AdminOpenAuth\Database\ClientRuleEntity;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Shopware\Core\Profiling\Profiler;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;

readonly class ClientRuleExecutor
{
    /**
     * @param array<string, RuleActionInterface> $actions
     */
    public function __construct(
        #[AutowireIterator('heptacom_open_auth.rule_action')]
        private iterable $actions,
        private ClientRuleValidator $clientRuleValidator,
        private LoggerInterface $logger,
    ) {
    }

    public function executeRules(ClientRuleCollection $rules, OAuthRuleScope $ruleScope): void
    {
        $shopwareUser = $ruleScope->getUser()->getExtensionOfType('shopwareUser', ArrayStruct::class) ?? new ArrayStruct();

        foreach ($this->actions as $action) {
            $traceName = 'heptacom-admin-open-auth::action-' . $action::getName() . '::execute-rules';

            Profiler::trace($traceName, function () use ($action, $rules, $ruleScope, $shopwareUser) {
                foreach ($rules->filterByActionName($action->getName()) as $rule) {
                    $isValid = $this->executeRule($action, $rule, $ruleScope, $shopwareUser['id'] ?? null);

                    if ($isValid && $rule->isStopOnMatch()) {
                        break;
                    }
                }
            });
        }
    }

    /**
     * @return bool Returns if the rule was validated true
     */
    protected function executeRule(
        RuleActionInterface $action,
        ClientRuleEntity $rule,
        OAuthRuleScope $ruleScope,
        ?string $userId
    ): bool
    {
        if ($this->clientRuleValidator->isValid($rule->getId(), $ruleScope)) {
            try {
                if ($userId === null) {
                    $action->preResolveUser($rule, $ruleScope);
                } else {
                    $action->postResolveUser($rule, $ruleScope, $userId);
                }
            } catch (\Throwable $e) {
                $this->logger->error(
                    \sprintf(
                        'Failed to execute action %s for rule %s, while trying to log in user "%s": %s',
                        $action->getName(),
                        $rule->getId(),
                        $ruleScope->getUser()->primaryKey,
                        $e->getMessage()
                    ),
                    [
                        'exception' => $e,
                        'actionName' => $action->getName(),
                        'ruleId' => $rule->getId(),
                        'user' => $ruleScope->getUser()->primaryKey,
                    ]
                );
            } finally {
                return true;
            }
        }

        return false;
    }
}
