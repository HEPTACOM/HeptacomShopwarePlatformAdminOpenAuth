<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\DevOps\PhpStan\Rule;

use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Interface_;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\ReflectionProvider;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * @implements Rule<Interface_>
 */
final readonly class InterfacesHaveDocumentationRule implements Rule
{
    public function __construct(private ReflectionProvider $reflectionProvider)
    {
    }

    public function getNodeType(): string
    {
        return Interface_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $reflectionClass = $this->reflectionProvider->getClass($scope->getNamespace() . '\\' . $node->name);
        $parentMethods = [];

        foreach ($reflectionClass->getInterfaces() as $interface) {
            foreach ($interface->getNativeReflection()->getMethods() as $method) {
                $name = $method->getName();
                $parentMethods[$name] = $name;
            }
        }

        foreach ($reflectionClass->getParents() as $parentClass) {
            foreach ($parentClass->getNativeReflection()->getMethods() as $method) {
                $name = $method->getName();
                $parentMethods[$name] = $name;
            }
        }

        $result = [];
        $methods = $node->getMethods();
        $methods = \array_filter($methods, static fn (ClassMethod $cm): bool => !\in_array($cm->name->toString(), $parentMethods, true));
        $interfaceNeedsDocumentation = \count($methods) !== 1;

        if ($interfaceNeedsDocumentation && $this->getCommentSummary($node) === '') {
            $result[] = RuleErrorBuilder::message('Interface must have a documentation')
                ->line($node->getStartLine())
                ->file($scope->getFile())
                ->build();
        }

        foreach ($node->getMethods() as $method) {
            if ($this->getCommentSummary($method) === '') {
                $result[] = RuleErrorBuilder::message(\sprintf('Interface method %s must have a documentation', $method->name))
                    ->line($method->getStartLine())
                    ->file($scope->getFile())
                    ->build();
            }
        }

        return $result;
    }

    private function getCommentSummary(Node $node): string
    {
        $commentSummary = '';

        foreach ($node->getComments() as $comment) {
            $commentLines = \explode("\n", (string) $comment);
            $commentLines = \array_map(
                static fn (string $l): string => \trim(\ltrim(\trim(\trim($l), '/'), '*')),
                $commentLines
            );
            $commentLines = \array_map(
                static fn (string $l): string => (string) \preg_replace('/@.*$/', '', $l),
                $commentLines
            );
            $commentSummary .= \implode('', $commentLines);
        }

        return $commentSummary;
    }
}
