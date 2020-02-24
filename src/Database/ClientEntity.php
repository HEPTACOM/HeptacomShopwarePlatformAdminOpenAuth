<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Database;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class ClientEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $provider;

    /**
     * @var array|null
     */
    protected $config;

    /**
     * @var LoginCollection|null
     */
    protected $logins;

    /**
     * @var UserEmailCollection|null
     */
    protected $userEmails;

    /**
     * @var UserKeyCollection|null
     */
    protected $userKeys;

    /**
     * @var UserTokenCollection|null
     */
    protected $userTokens;

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
