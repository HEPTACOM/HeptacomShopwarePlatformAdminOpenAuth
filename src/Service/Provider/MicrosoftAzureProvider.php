<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Component\Provider\MicrosoftAzureClient;
use Heptacom\AdminOpenAuth\Contract\ProviderInterface;
use Heptacom\AdminOpenAuth\Exception\ProvideClientInvalidConfigurationException;
use Heptacom\OpenAuth\Client\Contract\TokenPairFactoryContract;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Throwable;

class MicrosoftAzureProvider implements ProviderInterface
{
    /**
     * @var TokenPairFactoryContract
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

    /**
     * @var MicrosoftAzureConfigurationResolverFactory
     */
    private $configurationResolverFactory;

    public function __construct(
        TokenPairFactoryContract $tokenPairFactory,
        EntityRepositoryInterface $clientsRepository,
        RouterInterface $router,
        MicrosoftAzureConfigurationResolverFactory $configurationResolverFactory
    ) {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->clientsRepository = $clientsRepository;
        $this->router = $router;
        $this->configurationResolverFactory = $configurationResolverFactory;
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
                'clientId' => '',
                'clientSecret' => '',
                'redirectUri' => $this->router->generate('administration.heptacom.admin_open_auth.login', [
                    'clientId' => $clientId,
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'scopes' => [],
            ],
            'active' => false,
            'login' => true,
            'connect' => true,
            'store_user_token' => true,
            'provider' => 'microsoft_azure',
        ]], $context);
    }

    public function provideClient(string $clientId, array $config, Context $context): ClientInterface
    {
        try {
            $values = $this->configurationResolverFactory->getOptionResolver($clientId, $context)->resolve($config);
        } catch (Throwable $e) {
            throw new ProvideClientInvalidConfigurationException($clientId, self::class, $e->getMessage(), $e);
        }

        return new MicrosoftAzureClient($this->tokenPairFactory, $values);
    }
}
