<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Controller;

use Heptacom\AdminOpenAuth\ClientLoader;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\AdminOpenAuth\Database\LoginEntity;
use Heptacom\AdminOpenAuth\Login;
use Heptacom\AdminOpenAuth\UserResolver;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @RouteScope(scopes={"administration"})
 */
class AdministrationController extends AbstractController
{
    /**
     * @var EntityRepositoryInterface
     */
    private $clientsRepository;

    /**
     * @var ClientLoader
     */
    private $clientLoader;

    /**
     * @var UserResolver
     */
    private $userResolver;

    /**
     * @var Login
     */
    private $login;

    public function __construct(
        EntityRepositoryInterface $clientsRepository,
        ClientLoader $clientLoader,
        UserResolver $userResolver,
        Login $login
    ) {
        $this->clientsRepository = $clientsRepository;
        $this->clientLoader = $clientLoader;
        $this->userResolver = $userResolver;
        $this->login = $login;
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="administration.heptacom.admin_open_auth.login",
     *     path="/admin/open-auth/{clientId}/redirect",
     *     defaults={"auth_required"=false}
     * )
     */
    public function login(string $clientId, Request $request, Context $context): Response
    {
        $state = $request->query->get('state');
        $code = $request->query->get('code');
        $user = $this->clientLoader->load($clientId, $context)->getUser($state, $code);
        $this->userResolver->resolve($user, $state, $clientId, $context);
        $adminRoute = $this->generateUrl(
            'administration.index',
            ['state' => $state],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new RedirectResponse($adminRoute, Response::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="administration.heptacom.admin_open_auth.remote_login",
     *     path="/admin/open-auth/{clientId}/remote",
     *     defaults={"auth_required"=false}
     * )
     */
    public function remoteLogin(string $clientId, Context $context): Response
    {
        $state = Uuid::randomHex();
        $this->login->initiate($clientId, $state, $context);
        $target = $this->clientLoader->load($clientId, $context)->getLoginUrl($state);

        return RedirectResponse::create($target, Response::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="administration.heptacom.admin_open_auth.credentials",
     *     path="/admin/open-auth/credentials",
     *     defaults={"auth_required"=false}
     * )
     */
    public function getCredentials(Request $request, Context $context): JsonResponse
    {
        $state = $request->get('state');
        $login = $this->login->pop($state, $context);

        if (!$login instanceof LoginEntity) {
            return JsonResponse::create([], Response::HTTP_NOT_FOUND);
        }

        return JsonResponse::create([
            'username' => $login->getUser()->getUsername(),
            'password' => $login->getPassword(),
        ]);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="administration.heptacom.admin_open_auth.routes",
     *     path="/admin/open-auth/routes",
     *     defaults={"auth_required"=false}
     * )
     */
    public function clientRoutes(Context $context): JsonResponse
    {
        return JsonResponse::create([
            'clients' => array_values($this->clientsRepository
                ->search(new Criteria(), $context)
                ->getEntities()
                ->map(function (ClientEntity $client): array {
                    return [
                        'name' => $client->getName(),
                        'url' => $this->generateUrl(
                            'administration.heptacom.admin_open_auth.remote_login',
                            ['clientId' => $client->getId()],
                            UrlGeneratorInterface::ABSOLUTE_URL
                        ),
                    ];
                })),
        ]);
    }
}
