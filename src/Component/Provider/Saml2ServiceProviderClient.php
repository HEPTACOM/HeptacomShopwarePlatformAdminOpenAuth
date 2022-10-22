<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderService;
use Heptacom\AdminOpenAuth\Contract\MetadataClientContract;
use Heptacom\AdminOpenAuth\Contract\ModifiedRedirectBehaviourClientContract;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\Http\Message\RequestInterface;

class Saml2ServiceProviderClient extends ClientContract implements MetadataClientContract, ModifiedRedirectBehaviourClientContract
{
    public const AVAILABLE_USER_PROPERTIES = [
        'firstName',
        'lastName',
        'email',
        'timezone',
        'locale'
    ];

    private TokenPairFactoryContract $tokenPairFactory;

    private Saml2ServiceProviderService $saml2ServiceProviderService;

    public function __construct(TokenPairFactoryContract $tokenPairFactory, Saml2ServiceProviderService $saml2ServiceProviderService)
    {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->saml2ServiceProviderService = $saml2ServiceProviderService;
    }

    public function getLoginUrl(?string $state, RedirectBehaviour $behaviour): string
    {
        return $this->getInnerClient()->getAuthnRequestRedirectUri($state);
    }

    public function refreshToken(string $refreshToken): TokenPairStruct
    {
        /*return $this->tokenPairFactory->fromOpenIdConnectToken($this->getInnerClient()->getAccessToken('refresh_token', [
            'refresh_token' => $refreshToken,
        ]));*/
    }

    public function getUser(string $state, string $code, RedirectBehaviour $behaviour): UserStruct
    {
        $auth = $this->getInnerClient()->validateLoginConfirmData($code, $state);

        $user = new UserStruct();
        $user->setPrimaryKey($auth->getNameId());

        $mapping = $this->getInnerClient()->getConfig()->getAttributeMapping();
        foreach ($mapping as $property => $attributeName) {
            if (!in_array($property, self::AVAILABLE_USER_PROPERTIES) || $attributeName === '') {
                continue;
            }

            $propertyValues = $auth->getAttribute($attributeName) ?? [];
            $propertyValue = count($propertyValues) > 0 ? $propertyValues[array_key_first($propertyValues)] : null;

            if ($propertyValue === null) {
                continue;
            }

            switch($property) {
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
        // TODO: SAML: check if support is possible
        throw new \RuntimeException('Not supported');

        return $request;
    }

    public function getMetadataType(): string
    {
        return 'text/xml';
    }

    public function getMetadata(): string
    {
        // TODO: SAML: ensure that the redirect URI is set correctly
        return $this->getInnerClient()->getServiceProviderMetadata();
    }

    public function modifyRedirectBehaviour(RedirectBehaviour $behaviour): void
    {
        $behaviour->setStateKey('RelayState');
        $behaviour->setCodeKey('SAMLResponse');
    }

    public function getInnerClient(): Saml2ServiceProviderService
    {
        return $this->saml2ServiceProviderService;
    }
}
