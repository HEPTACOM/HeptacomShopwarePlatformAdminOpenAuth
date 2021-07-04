<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ClientEntity extends Entity
{
    use EntityIdTrait;

    protected ?string $name = null;

    protected ?string $provider = null;

    protected ?bool $active = null;

    protected ?bool $login = null;

    protected ?bool $connect = null;

    protected ?bool $storeUserToken = null;

    protected ?array $config = null;

    protected ?LoginCollection $logins = null;

    protected ?UserEmailCollection $userEmails = null;

    protected ?UserKeyCollection $userKeys = null;

    protected ?UserTokenCollection $userTokens = null;

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getProvider(): ?string
    {
        return $this->provider;
    }

    public function setProvider(?string $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getLogin(): ?bool
    {
        return $this->login;
    }

    public function setLogin(?bool $login): self
    {
        $this->login = $login;

        return $this;
    }

    public function getConnect(): ?bool
    {
        return $this->connect;
    }

    public function setConnect(?bool $connect): self
    {
        $this->connect = $connect;

        return $this;
    }

    public function getStoreUserToken(): ?bool
    {
        return $this->storeUserToken;
    }

    public function setStoreUserToken(?bool $storeUserToken): self
    {
        $this->storeUserToken = $storeUserToken;

        return $this;
    }

    public function getConfig(): ?array
    {
        return $this->config;
    }

    public function setConfig(?array $config): self
    {
        $this->config = $config;

        return $this;
    }

    public function getLogins(): ?LoginCollection
    {
        return $this->logins;
    }

    public function setLogins(?LoginCollection $logins): self
    {
        $this->logins = $logins;

        return $this;
    }

    public function getUserEmails(): ?UserEmailCollection
    {
        return $this->userEmails;
    }

    public function setUserEmails(?UserEmailCollection $userEmails): ClientEntity
    {
        $this->userEmails = $userEmails;

        return $this;
    }

    public function getUserKeys(): ?UserKeyCollection
    {
        return $this->userKeys;
    }

    public function setUserKeys(?UserKeyCollection $userKeys): self
    {
        $this->userKeys = $userKeys;

        return $this;
    }

    public function getUserTokens(): ?UserTokenCollection
    {
        return $this->userTokens;
    }

    public function setUserTokens(?UserTokenCollection $userTokens): self
    {
        $this->userTokens = $userTokens;

        return $this;
    }
}
