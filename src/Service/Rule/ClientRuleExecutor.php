<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Rule;

use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleActionInterface;
use Heptacom\AdminOpenAuth\Database\ClientRuleCollection;
use Psr\Log\LoggerInterface;
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

    public function executeRules(ClientRuleCollection $rules, OAuthRuleScope $ruleScope): void {
        foreach ($this->actions as $action) {
            foreach ($rules->filterByActionName($action->getName()) as $rule) {
                if ($this->clientRuleValidator->isValid($rule->getId(), $ruleScope)) {
                    try {
                        $action->execute($rule, $ruleScope);
                    } catch (\Throwable $e) {
                        $this->logger->error(
                            sprintf(
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
                    }

                    if ($rule->isStopOnMatch()) {
                        break;
                    }
                }
            }
        }
    }
}
