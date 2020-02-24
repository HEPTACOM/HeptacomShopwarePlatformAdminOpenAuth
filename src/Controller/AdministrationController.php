<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Controller;

use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
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
     * @var OpenAuthenticationFlowInterface
     */
    private $flow;

    public function __construct(OpenAuthenticationFlowInterface $flow)
    {
        $this->flow = $flow;
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="administration.heptacom.admin_open_auth.login",
     *     path="/admin/open-auth/{clientId}/redirect",
     *     defaults={"auth_required" = false}
     * )
     */
    public function login(string $clientId, Request $request, Context $context): Response
    {
        $state = $request->query->get('state');
        $code = $request->query->get('code');
        $this->flow->upsertUser($clientId, $state, $code, $context);

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
     *     defaults={"auth_required" = false}
     * )
     */
    public function remoteLogin(string $clientId, Context $context): Response
    {
        return RedirectResponse::create(
            $this->flow->getRedirectUrl($clientId, $context),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="administration.heptacom.admin_open_auth.credentials",
     *     path="/admin/open-auth/credentials",
     *     defaults={"auth_required" = false}
     * )
     */
    public function getCredentials(Request $request, Context $context): JsonResponse
    {
        $state = $request->get('state');
        $login = $this->flow->popCredentials($state, $context);

        return JsonResponse::create($login, empty($login) ? Response::HTTP_NOT_FOUND : Response::HTTP_OK);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="administration.heptacom.admin_open_auth.routes",
     *     path="/admin/open-auth/routes",
     *     defaults={"auth_required" = false}
     * )
     */
    public function clientRoutes(Context $context): JsonResponse
    {
        return JsonResponse::create(['clients' => $this->flow->getLoginRoutes($context)]);
    }
}
