<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect;

use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Shopware\Core\Framework\Struct\Struct;

final class OpenIdConnectToken extends Struct
{
    protected ?string $access_token = null;

    protected ?int $expires_in = null;

    protected ?string $id_token = null;

    protected ?int $refresh_expires_in = null;

    protected ?string $refresh_token = null;

    protected ?string $token_type = '';

    private JWSSerializerManager $serializerManager;

    public function __construct()
    {
        $this->serializerManager = new JWSSerializerManager([
            new CompactSerializer(),
        ]);
    }

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

    public function getIdToken(): ?string
    {
        return $this->id_token;
    }

    public function setIdToken(?string $id_token): OpenIdConnectToken
    {
        $this->id_token = $id_token;

        return $this;
    }

    public function getIdTokenPayload(): ?array
    {
        if ($this->id_token === null) {
            return null;
        }

        $idTokenPayload = $this->serializerManager->unserialize($this->id_token)->getPayload();
        if ($idTokenPayload === null) {
            return [];
        }

        $decodedPayload = \json_decode($idTokenPayload, true, 512, \JSON_THROW_ON_ERROR);
        if (!\is_array($decodedPayload)) {
            throw new \InvalidArgumentException('Invalid id_token payload. Expected JSON object.', 1739779042);
        }

        return $decodedPayload;
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
