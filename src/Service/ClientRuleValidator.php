<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Doctrine\DBAL\Connection;
use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Database\ClientRuleConditionDefinition;
use Shopware\Core\Content\Rule\DataAbstractionLayer\Indexing\ConditionTypeNotFound;
use Shopware\Core\Framework\Rule\Collector\RuleConditionRegistry;
use Shopware\Core\Framework\Rule\Container\AndRule;
use Shopware\Core\Framework\Rule\Container\ContainerInterface;
use Shopware\Core\Framework\Rule\Rule;
use Shopware\Core\Framework\Rule\ScriptRule;
use Shopware\Core\Framework\Uuid\Uuid;

class ClientRuleValidator
{
    public function __construct(
        private readonly Connection $connection,
        private readonly RuleConditionRegistry $ruleConditionRegistry
    ) {
    }

    public function isValid(string $clientRuleId, OAuthRuleScope $scope): bool
    {
        $rule = $this->buildRule($clientRuleId);

        return $rule->match($scope);
    }

    public function buildRule(string $clientRuleId): Rule
    {
        $conditions = $this->connection->createQueryBuilder()
            ->select('*')
            ->from(ClientRuleConditionDefinition::ENTITY_NAME)
            ->where('client_rule_id = :clientRuleId')
            ->orderBy('position')
            ->setParameter('clientRuleId', Uuid::fromHexToBytes($clientRuleId))
            ->fetchAllAssociative();

        $nested = $this->buildNested($conditions, null);

        return new AndRule($nested);
    }

    /**
     * Mostly copied from Shopware.
     *
     * @see \Shopware\Core\Content\Rule\DataAbstractionLayer\RulePayloadUpdater::buildNested()
     */
    private function buildNested(array $rules, ?string $parentId): array
    {
        $nested = [];
        foreach ($rules as $rule) {
            if ($rule['parent_id'] !== $parentId) {
                continue;
            }

            if (!$this->ruleConditionRegistry->has($rule['type'])) {
                throw new ConditionTypeNotFound($rule['type']);
            }

            $ruleClass = $this->ruleConditionRegistry->getRuleClass($rule['type']);
            $object = new $ruleClass();

            if ($object instanceof ScriptRule) {
                // HEPTACOM: Script rules are not supported
                continue;
            }

            if ($rule['value'] !== null) {
                $object->assign(\json_decode((string) $rule['value'], true, 512, \JSON_THROW_ON_ERROR));
            }

            if ($object instanceof ContainerInterface) {
                $children = $this->buildNested($rules, $rule['id']);
                foreach ($children as $child) {
                    $object->addRule($child);
                }
            }

            // BEGIN HEPTACOM CHANGE
            // try to wake up the object, as some rules only then deserialize all data
            if (\method_exists($object, '__wakeup')) {
                $object->__wakeup();
            }
            // END HEPTACOM CHANGE

            $nested[] = $object;
        }

        return $nested;
    }
}
