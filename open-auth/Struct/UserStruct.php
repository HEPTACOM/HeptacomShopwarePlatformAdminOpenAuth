<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Struct;

class UserStruct
{
    /**
     * @var string
     */
    protected $primaryEmail;

    /**
     * @var array|string[]
     */
    protected $emails = [];

    /**
     * @var string
     */
    protected $displayName;

    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * @var TokenPairStruct|null
     */
    protected $tokenPair;

    protected $passthrough = [];

    public function getPrimaryEmail(): string
    {
        return $this->primaryEmail;
    }

    public function setPrimaryEmail(string $primaryEmail): self
    {
        $this->primaryEmail = $primaryEmail;

        return $this;
    }

    public function getEmails(): array
    {
        return $this->emails;
    }

    public function setEmails(array $emails): self
    {
        $this->emails = $emails;

        return $this;
    }

    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function setPrimaryKey(string $primaryKey): self
    {
        $this->primaryKey = $primaryKey;

        return $this;
    }

    public function getTokenPair(): ?TokenPairStruct
    {
        return $this->tokenPair;
    }

    public function setTokenPair(?TokenPairStruct $tokenPair): self
    {
        $this->tokenPair = $tokenPair;

        return $this;
    }

    public function getPassthrough(): array
    {
        return $this->passthrough;
    }

    public function addPassthrough(string $key, $value): self
    {
        $this->passthrough[$key] = $value;

        return $this;
    }

    public function setPassthrough(array $passthrough): self
    {
        $this->passthrough = $passthrough;

        return $this;
    }
}
