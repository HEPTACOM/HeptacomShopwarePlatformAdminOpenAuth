<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect;

use Shopware\Core\Framework\Struct\Struct;

final class OpenIdConnectUser extends Struct
{
    protected string $sub = '';

    protected ?string $name = null;

    protected ?string $given_name = null;

    protected ?string $family_name = null;

    protected ?string $middle_name = null;

    protected ?string $nickname = null;

    protected ?string $preferred_username = null;

    protected ?string $profile = null;

    protected ?string $picture = null;

    protected ?string $website = null;

    protected ?string $email = null;

    protected ?bool $email_verified = null;

    protected ?string $gender = null;

    protected ?string $birthdate = null;

    protected ?string $zoneinfo = null;

    protected ?string $locale = null;

    protected ?string $phone_number = null;

    protected ?bool $phone_number_verified = null;

    protected ?array $address = null;

    protected ?int $updated_at = null;

    public function getSub(): string
    {
        return $this->sub;
    }

    public function setSub(string $sub): void
    {
        $this->sub = $sub;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getGivenName(): ?string
    {
        return $this->given_name;
    }

    public function setGivenName(?string $given_name): void
    {
        $this->given_name = $given_name;
    }

    public function getFamilyName(): ?string
    {
        return $this->family_name;
    }

    public function setFamilyName(?string $family_name): void
    {
        $this->family_name = $family_name;
    }

    public function getMiddleName(): ?string
    {
        return $this->middle_name;
    }

    public function setMiddleName(?string $middle_name): void
    {
        $this->middle_name = $middle_name;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): void
    {
        $this->nickname = $nickname;
    }

    public function getPreferredUsername(): ?string
    {
        return $this->preferred_username;
    }

    public function setPreferredUsername(?string $preferred_username): void
    {
        $this->preferred_username = $preferred_username;
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(?string $profile): void
    {
        $this->profile = $profile;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): void
    {
        $this->picture = $picture;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getEmailVerified(): ?bool
    {
        return $this->email_verified;
    }

    public function setEmailVerified(?bool $email_verified): void
    {
        $this->email_verified = $email_verified;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    public function getBirthdate(): ?string
    {
        return $this->birthdate;
    }

    public function setBirthdate(?string $birthdate): void
    {
        $this->birthdate = $birthdate;
    }

    public function getZoneinfo(): ?string
    {
        return $this->zoneinfo;
    }

    public function setZoneinfo(?string $zoneinfo): void
    {
        $this->zoneinfo = $zoneinfo;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phone_number;
    }

    public function setPhoneNumber(?string $phone_number): void
    {
        $this->phone_number = $phone_number;
    }

    public function getPhoneNumberVerified(): ?bool
    {
        return $this->phone_number_verified;
    }

    public function setPhoneNumberVerified(?bool $phone_number_verified): void
    {
        $this->phone_number_verified = $phone_number_verified;
    }

    public function getAddress(): ?array
    {
        return $this->address;
    }

    public function setAddress(?array $address): void
    {
        $this->address = $address;
    }

    public function getUpdatedAt(): ?int
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?int $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}
