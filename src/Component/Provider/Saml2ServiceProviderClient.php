<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderService;
use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\MetadataClientContract;
use Heptacom\AdminOpenAuth\Contract\ModifiedRedirectBehaviourClientContract;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\Http\Message\RequestInterface;

final class Saml2ServiceProviderClient extends ClientContract implements MetadataClientContract, ModifiedRedirectBehaviourClientContract
{
    public const AVAILABLE_USER_PROPERTIES = [
        'firstName',
        'lastName',
        'email',
        'timezone',
        'locale',
    ];

    private Saml2ServiceProviderService $saml2ServiceProviderService;

    public function __construct(Saml2ServiceProviderService $saml2ServiceProviderService)
    {
        $this->saml2ServiceProviderService = $saml2ServiceProviderService;
    }

    public function getLoginUrl(?string $state, RedirectBehaviour $behaviour): string
    {
        return $this->getInnerClient()->getAuthnRequestRedirectUri($state);
    }

    public function refreshToken(string $refreshToken): TokenPairStruct
    {
        throw new \Exception('Not supported.');
    }

    public function getUser(string $state, string $code, RedirectBehaviour $behaviour): UserStruct
    {
        $auth = $this->getInnerClient()->validateLoginConfirmData($code, $state);

        $user = new UserStruct();
        $user->setPrimaryKey($auth->getNameId());

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
                    $user->setFirstName($propertyValue);

                    break;

                case 'lastName':
                    $user->setLastName($propertyValue);

                    break;

                case 'email':
                    $user->setPrimaryEmail($propertyValue);
                    $user->setEmails($propertyValues);

                    break;

                case 'timezone':
                    $user->setTimezone($propertyValue);

                    break;

                case 'locale':
                    $user->setLocale($propertyValue);

                    break;
            }
        }

        return $user;
    }

    public function authorizeRequest(RequestInterface $request, TokenPairStruct $token): RequestInterface
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
