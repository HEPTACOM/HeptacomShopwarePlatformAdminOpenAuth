<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderService;
use Heptacom\AdminOpenAuth\Component\Saml\Saml2UserData;
use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\MetadataClientContract;
use Heptacom\AdminOpenAuth\Contract\ModifiedRedirectBehaviourClientContract;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\AdminOpenAuth\Contract\TokenPair;
use Heptacom\AdminOpenAuth\Contract\User;
use Psr\Http\Message\RequestInterface;

final class Saml2ServiceProviderClient extends ClientContract implements MetadataClientContract, ModifiedRedirectBehaviourClientContract
{
    public const AVAILABLE_USER_PROPERTIES = [
        'firstName',
        'lastName',
        'email',
        'timezone',
        'locale',
        'roles',
    ];

    public function __construct(
        private readonly Saml2ServiceProviderService $saml2ServiceProviderService
    ) {
    }

    public function getLoginUrl(?string $state, RedirectBehaviour $behaviour): string
    {
        return $this->getInnerClient()->getAuthnRequestRedirectUri($state);
    }

    public function refreshToken(string $refreshToken): TokenPair
    {
        throw new \Exception('Not supported.');
    }

    public function getUser(string $state, string $code, RedirectBehaviour $behaviour): User
    {
        $auth = $this->getInnerClient()->validateLoginConfirmData($code, $state);

        $user = new User();
        $user->primaryKey = $auth->getNameId();

        $userData = new Saml2UserData();
        $user->addExtension(Saml2UserData::class, $userData);

        $mapping = $this->getInnerClient()->getConfig()->getAttributeMapping();
        foreach ($mapping as $property => $attributeName) {
            if (!\in_array($property, self::AVAILABLE_USER_PROPERTIES, true) || $attributeName === '') {
                continue;
            }

            $propertyValues = $auth->getAttribute($attributeName) ?? $auth->getAttributeWithFriendlyName($attributeName) ?? [];
            $propertyValue = \count($propertyValues) > 0 ? $propertyValues[array_key_first($propertyValues)] : null;

            if ($propertyValue === null) {
                continue;
            }

            switch ($property) {
                case 'firstName':
                    $user->firstName = $propertyValue;

                    break;

                case 'lastName':
                    $user->lastName = $propertyValue;

                    break;

                case 'email':
                    $user->primaryEmail = $propertyValue;
                    $user->emails = $propertyValues;

                    break;

                case 'timezone':
                    $user->timezone = $propertyValue;

                    break;

                case 'locale':
                    $user->locale = $propertyValue;

                    break;

                case 'roles':
                    $userData->roles = $propertyValues;

                    break;
            }
        }

        return $user;
    }

    public function authorizeRequest(RequestInterface $request, TokenPair $token): RequestInterface
    {
        throw new \RuntimeException('Not supported');
    }

    public function getMetadataType(): string
    {
        return 'application/xml';
    }

    public function getMetadata(): string
    {
        return $this->getInnerClient()->getServiceProviderMetadata();
    }

    public function modifyRedirectBehaviour(RedirectBehaviour $behaviour): void
    {
        $behaviour->stateKey = 'RelayState';
        $behaviour->codeKey = 'SAMLResponse';
    }

    public function getInnerClient(): Saml2ServiceProviderService
    {
        return $this->saml2ServiceProviderService;
    }
}
