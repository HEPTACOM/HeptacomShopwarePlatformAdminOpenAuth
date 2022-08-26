<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect;

use Shopware\Core\Framework\Struct\Struct;

class OpenIdConnectToken extends Struct
{
    protected ?string $access_token = null;

    protected ?int $expires_in = null;

    protected ?int $refresh_expires_in = null;

    protected ?string $refresh_token = null;

    protected ?string $token_type = '';

    public function getAccessToken(): ?string
    {
        return $this->access_token;
    }

    public function setAccessToken(?string $access_token): void
    {
        $this->access_token = $access_token;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expires_in;
    }

    public function setExpiresIn(?int $expires_in): void
    {
        $this->expires_in = $expires_in;
    }

    public function getRefreshExpiresIn(): ?int
    {
        return $this->refresh_expires_in;
    }

    public function setRefreshExpiresIn(?int $refresh_expires_in): void
    {
        $this->refresh_expires_in = $refresh_expires_in;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refresh_token;
    }

    public function setRefreshToken(?string $refresh_token): void
    {
        $this->refresh_token = $refresh_token;
    }

    public function getTokenType(): ?string
    {
        return $this->token_type;
    }

    public function setTokenType(?string $token_type): void
    {
        $this->token_type = $token_type;
    }
}
