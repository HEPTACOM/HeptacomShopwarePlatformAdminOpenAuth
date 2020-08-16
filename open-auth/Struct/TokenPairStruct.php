<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Struct;

use DateTimeInterface;

class TokenPairStruct
{
    /**
     * @var string|null
     */
    protected $accessToken;

    /**
     * @var string|null
     */
    protected $refreshToken;

    /**
     * @var DateTimeInterface|null
     */
    protected $expiresAt;

    protected $passthrough = [];

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(?DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

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
