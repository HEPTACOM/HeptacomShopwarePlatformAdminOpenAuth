<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Heptacom\AdminOpenAuth\Contract\UserResolverInterface;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\AdminOpenAuth\Database\LoginEntity;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class OpenAuthenticationFlow implements OpenAuthenticationFlowInterface
{
    /**
     * @var LoginInterface
     */
    private $login;

    /**
     * @var ClientLoaderInterface
     */
    private $clientLoader;

    /**
     * @var UserResolverInterface
     */
    private $userResolver;

    /**
     * @var EntityRepositoryInterface
     */
    private $clientsRepository;

    /**
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        LoginInterface $login,
        ClientLoaderInterface $clientLoader,
        UserResolverInterface $userResolver,
        EntityRepositoryInterface $clientsRepository,
        RouterInterface $router
    ) {
        $this->login = $login;
        $this->clientLoader = $clientLoader;
        $this->userResolver = $userResolver;
        $this->clientsRepository = $clientsRepository;
        $this->router = $router;
    }

    public function getRedirectUrl(string $clientId, Context $context): string
    {
        if (!$this->clientLoader->canLogin($clientId, $context)) {
            throw new LoadClientException('Client can not login', $clientId);
        }

        $state = Uuid::randomHex();
        $this->login->initiate($clientId, null, $state, $context);

        return $this->clientLoader->load($clientId, $context)->getLoginUrl($state);
    }

    public function getRedirectUrlToConnect(string $clientId, string $userId, Context $context): string
    {
        if (!$this->clientLoader->canConnect($clientId, $context)) {
            throw new LoadClientException('Client can not connect', $clientId);
        }

        $state = Uuid::randomHex();
        $this->login->initiate($clientId, $userId, $state, $context);

        return $this->clientLoader->load($clientId, $context)->getLoginUrl($state);
    }

    public function upsertUser(string $clientId, string $state, string $code, Context $context): void
    {
        $user = $this->clientLoader->load($clientId, $context)->getUser($state, $code);
        $this->userResolver->resolve($user, $state, $clientId, $context);
    }

    public function getLoginRoutes(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('active', true),
            new EqualsFilter('login', true)
        );

        return array_values($this->clientsRepository
            ->search($criteria, $context)
            ->getEntities()
            ->map(function (ClientEntity $client): array {
                return [
                    'name' => $client->getName(),
                    'url' => $this->router->generate(
                        'administration.heptacom.admin_open_auth.remote_login',
                        ['clientId' => $client->getId()],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    ),
                ];
            }));
    }
}
