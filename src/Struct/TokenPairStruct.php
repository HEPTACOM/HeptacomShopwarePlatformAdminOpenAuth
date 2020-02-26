<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Struct;

use DateTimeInterface;
use Shopware\Core\Framework\Struct\Struct;

class TokenPairStruct extends Struct
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
}
