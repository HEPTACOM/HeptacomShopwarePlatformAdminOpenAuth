<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Provider;

use Heptacom\AdminOpenAuth\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Contract\ProviderInterface;
use Heptacom\AdminOpenAuth\Contract\TokenPairFactoryInterface;
use Heptacom\AdminOpenAuth\Exception\ProvideClientInvalidConfigurationException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class MicrosoftAzureProvider implements ProviderInterface
{
    /**
     * @var TokenPairFactoryInterface
     */
    private $tokenPairFactory;

    /**
     * @var EntityRepositoryInterface
     */
    private $clientsRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        TokenPairFactoryInterface $tokenPairFactory,
        EntityRepositoryInterface $clientsRepository,
        RouterInterface $router
    ) {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->clientsRepository = $clientsRepository;
        $this->router = $router;
    }

    public function provides(): string
    {
        return 'microsoft_azure';
    }

    public function initializeClientConfiguration(string $clientId, Context $context): void
    {
        $this->clientsRepository->update([[
            'id' => $clientId,
            'config' => [
                'appId' => '',
                'appSecret' => '',
                'redirectUri' => $this->router->generate('administration.heptacom.admin_open_auth.login', [
                    'clientId' => $clientId,
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'scopes' => [],
                'storeToken' => true,
            ],
            'active' => false,
            'login' => true,
            'connect' => true,
            'provider' => 'microsoft_azure',
        ]], $context);
    }

    public function provideClient(string $clientId, array $config, Context $context): ClientInterface
    {
        if (!array_key_exists('appId', $config)) {
            throw new ProvideClientInvalidConfigurationException($clientId, self::class, 'appId missing');
        }

        if (!array_key_exists('appSecret', $config)) {
            throw new ProvideClientInvalidConfigurationException($clientId, self::class, 'appSecret missing');
        }

        if (!array_key_exists('redirectUri', $config)) {
            throw new ProvideClientInvalidConfigurationException($clientId, self::class, 'redirectUri missing');
        }

        $storeToken = array_key_exists('storeToken', $config) && $config['storeToken'];
        $scopes = array_key_exists('scopes', $config) ? $config['scopes'] : [];

        $appId = $config['appId'];
        $appSecret = $config['appSecret'];
        $redirectUri = $config['redirectUri'];

        return new MicrosoftAzureClient($this->tokenPairFactory, $appId, $appSecret, $redirectUri, $storeToken, $scopes);
    }
}
