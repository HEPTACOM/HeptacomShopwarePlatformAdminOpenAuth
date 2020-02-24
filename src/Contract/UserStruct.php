<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Struct\Struct;

class UserStruct extends Struct
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
}
