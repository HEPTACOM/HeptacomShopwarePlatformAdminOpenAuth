<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Contract\ClientInterface;
use Heptacom\AdminOpenAuth\Component\Provider\JiraClient;
use Heptacom\AdminOpenAuth\Contract\ProviderInterface;
use Heptacom\AdminOpenAuth\Contract\TokenPairFactoryInterface;
use Heptacom\AdminOpenAuth\Exception\ProvideClientInvalidConfigurationException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Throwable;

class JiraProvider implements ProviderInterface
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

    /**
     * @var JiraConfigurationResolverFactory
     */
    private $configurationResolverFactory;

    public function __construct(
        TokenPairFactoryInterface $tokenPairFactory,
        EntityRepositoryInterface $clientsRepository,
        RouterInterface $router,
        JiraConfigurationResolverFactory $configurationResolverFactory
    ) {
        $this->tokenPairFactory = $tokenPairFactory;
        $this->clientsRepository = $clientsRepository;
        $this->router = $router;
        $this->configurationResolverFactory = $configurationResolverFactory;
    }

    public function provides(): string
    {
        return 'jira';
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
            'provider' => 'jira',
        ]], $context);
    }

    public function provideClient(string $clientId, array $config, Context $context): ClientInterface
    {
        try {
            $values = $this->configurationResolverFactory->getOptionResolver($clientId, $context)->resolve($config);
        } catch (Throwable $e) {
            throw new ProvideClientInvalidConfigurationException($clientId, self::class, $e->getMessage(), $e);
        }

        return new JiraClient($this->tokenPairFactory, $values);
    }
}
