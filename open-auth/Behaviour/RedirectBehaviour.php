<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Behaviour;

class RedirectBehaviour
{
    const DEFAULT_EXPECT_STATE = false;

    const DEFAULT_CODE_KEY = 'code';

    const DEFAULT_STATE_KEY = 'state';

    /**
     * @var bool
     */
    private $expectState;

    /**
     * @var string
     */
    private $codeKey;

    /**
     * @var string
     */
    private $stateKey;

    public function __construct(?bool $expectState = null, ?string $codeKey = null, ?string $stateKey = null)
    {
        $this->expectState = $expectState ?? self::DEFAULT_EXPECT_STATE;
        $this->codeKey = $codeKey ?? self::DEFAULT_CODE_KEY;
        $this->stateKey = $stateKey ?? self::DEFAULT_STATE_KEY;
    }

    public function isExpectState(): bool
    {
        return $this->expectState;
    }

    public function setExpectState(?bool $expectState = null): self
    {
        $this->expectState = $expectState ?? self::DEFAULT_EXPECT_STATE;

        return $this;
    }

    public function getCodeKey(): string
    {
        return $this->codeKey;
    }

    public function setCodeKey(?string $codeKey = null): self
    {
        $this->codeKey = $codeKey ?? self::DEFAULT_CODE_KEY;

        return $this;
    }

    public function getStateKey(): string
    {
        return $this->stateKey;
    }

    public function setStateKey(?string $stateKey = null): self
    {
        $this->stateKey = $stateKey ?? self::DEFAULT_STATE_KEY;

        return $this;
    }
}
