<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect\Rule;

use JmesPath\Env as JmesPath;

trait JMESPathValidation
{
    protected function matchExpression(string $expression, array $data): bool
    {
        try {
            $evaluatedExpression = JmesPath::search($expression, $data);

            return $this->validateExpressionResult($evaluatedExpression);
        } catch (\Throwable) {
            return false;
        }
    }

    protected function validateExpressionResult($evaluatedExpression): bool
    {
        return match (\gettype($evaluatedExpression)) {
            'NULL' => false,
            'boolean' => $evaluatedExpression,
            'integer' => $evaluatedExpression !== 0,
            'double' => $evaluatedExpression !== 0.0,
            'string' => $evaluatedExpression !== '',
            'array' => \count($evaluatedExpression) > 0,
            default => false,
        };
    }
}
