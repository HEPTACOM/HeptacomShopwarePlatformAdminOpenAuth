<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Route\Exception;

use Throwable;

class RedirectReceiveMissingStateException extends RedirectReceiveException
{
    /**
     * @var array
     */
    private $queryParams;

    /**
     * @var string
     */
    private $stateKey;

    public function __construct(array $queryParams, string $stateKey, ?Throwable $previous = null)
    {
        parent::__construct('No state in request found', 0, $previous);
        $this->queryParams = $queryParams;
        $this->stateKey = $stateKey;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getStateKey(): string
    {
        return $this->stateKey;
    }
}
