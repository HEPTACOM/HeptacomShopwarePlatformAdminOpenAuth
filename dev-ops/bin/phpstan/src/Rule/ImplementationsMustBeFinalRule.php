<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\DevOps\PhpStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements \PHPStan\Rules\Rule<\PhpParser\Node\Stmt\Class_>
 */
final class ImplementationsMustBeFinalRule implements Rule
{
    public function getNodeType(): string
    {
        return Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->isStructAlike($node)) {
            if (!$this->canBeFinal($node)) {
                return [];
            }

            if (!$node->isFinal()) {
                return [
                    RuleErrorBuilder::message(\sprintf('Class \'%s\' that looks like a struct is expected to be final', $node->namespacedName))
                        ->line($node->getStartLine())
                        ->file($scope->getFile())
                        ->build(),
                ];
            }

            return [];
        }

        if (!$this->canBeFinal($node)) {
            return [];
        }

        if ($node->isFinal()) {
            if ($node->implements === [] && $node->extends === null) {
                return [
                    RuleErrorBuilder::message(\sprintf('Class \'%s\' that is final neither looks like a struct, extends a contract nor implements an interface', $node->namespacedName))
                        ->line($node->getStartLine())
                        ->file($scope->getFile())
                        ->build(),
                ];
            }

            return [];
        }

        if ($node->extends !== null) {
            $extends = (string) $node->extends;

            if (\str_ends_with($extends, 'Contract')) {
                return [
                    RuleErrorBuilder::message(\sprintf('Class \'%s\' extends a contract must be final or abstract', $node->namespacedName))
                        ->line($node->getStartLine())
                        ->file($scope->getFile())
                        ->build(),
                ];
            }
        }

        if ($node->implements === []) {
            return [];
        }

        return [
            RuleErrorBuilder::message(\sprintf('Class \'%s\' implements an interface must be final or abstract', $node->namespacedName))
                ->line($node->getStartLine())
                ->file($scope->getFile())
                ->build(),
        ];
    }

    private function canBeFinal(Class_ $class): bool
    {
        /* @see https://github.com/nikic/PHP-Parser/issues/821 */
        if ($class->isAnonymous() || \str_starts_with((string) $class->name, 'AnonymousClass')) {
            return false;
        }

        if ($class->isAbstract() || \str_starts_with((string) $class->name, 'Contract')) {
            return false;
        }

        // soft limit
        if (\is_a((string) $class->namespacedName, \Throwable::class, true)) {
            return false;
        }

        return true;
    }

    private function isStructAlike(Class_ $class): bool
    {
        /** @var class-string[] $interfaces */
        $interfaces = \array_map('strval', $class->implements);
        // soft limit
        $interfaces = \array_filter($interfaces, static fn (string $i) => $i !== \JsonSerializable::class);
        $interfaces = \array_filter($interfaces, static fn (string $i) => !\str_ends_with($i, 'AwareInterface'));
        $interfaces = \array_filter($interfaces, static fn (string $i) => (new \ReflectionClass($i))->getMethods() !== []);

        if ($interfaces !== []) {
            return false;
        }

        if ($class->extends !== null) {
            return false;
        }

        /** @var ClassMethod[] $setter */
        $setter = [];
        /** @var ClassMethod[] $getter */
        $getter = [];

        foreach ($class->getMethods() as $method) {
            if ($method->isMagic()) {
                continue;
            }

            if ($method->isPrivate()) {
                return false;
            }

            if ($method->isStatic()) {
                continue;
            }

            if (((string) $method->name) === 'jsonSerialize') {
                continue;
            }

            if (\str_starts_with((string) $method->name, 'with')) {
                $setter[] = \lcfirst(\mb_substr((string) $method->name, 4));

                continue;
            }

            if (\str_starts_with((string) $method->name, 'set')) {
                $setter[] = \lcfirst(\mb_substr((string) $method->name, 3));

                continue;
            }

            if (\str_starts_with((string) $method->name, 'get')) {
                $getter[] = \lcfirst(\mb_substr((string) $method->name, 3));

                continue;
            }

            return false;
        }

        $constructor = $class->getMethod('__construct');

        if ($constructor instanceof ClassMethod) {
            /** @var Node\Param $param */
            foreach ($constructor->getParams() as $param) {
                $paramVar = $param->var;

                if ($paramVar instanceof Node\Expr\Error) {
                    throw new \LogicException('Unexpected error type');
                }

                $paramName = $paramVar->name;

                if (!\is_string($paramName)) {
                    $setter[] = $paramName;
                }
            }
        }

        if ($getter === [] || $setter === []) {
            return false;
        }

        $getter = \array_unique($getter);
        $setter = \array_unique($setter);

        \sort($getter);
        \sort($setter);

        return $getter === $setter;
    }
}
