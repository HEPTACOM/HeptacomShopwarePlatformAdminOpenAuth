<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Controller;

use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Heptacom\AdminOpenAuth\Contract\ProviderRepositoryInterface;
use Heptacom\AdminOpenAuth\Database\ClientDefinition;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Response\ResponseFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
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
     *     name="administration.heptacom.admin_open_auth.routes",
     *     path="/admin/open-auth/routes",
     *     defaults={"auth_required" = false}
     * )
     */
    public function clientRoutes(Context $context): JsonResponse
    {
        return JsonResponse::create(['clients' => $this->flow->getLoginRoutes($context)]);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="api.heptacom.admin_open_auth.remote_connect",
     *     path="/api/v{version}/_admin/open-auth/{clientId}/connect"
     * )
     */
    public function remoteConnect(string $clientId, Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        return JsonResponse::create([
            'target' => $this->flow->getRedirectUrlToConnect($clientId, $adminApiSource->getUserId(), $context),
        ]);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="api.heptacom.admin_open_auth.provider.list",
     *     path="/api/v{version}/_action/heptacom_admin_open_auth_provider/list"
     * )
     */
    public function providerList(ProviderRepositoryInterface $providerRepository): Response
    {
        return JsonResponse::create([
            'data' => $providerRepository->getProviderKeys(),
        ]);
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="api.heptacom.admin_open_auth.provider.factorize",
     *     path="/api/v{version}/_action/heptacom_admin_open_auth_provider/factorize"
     * )
     */
    public function createClient(
        Request $request,
        ResponseFactoryInterface $responseFactory,
        ClientDefinition $definition,
        EntityRepositoryInterface $clientsRepository,
        ClientLoaderInterface $clientLoader,
        Context $context
    ): Response {
        $providerKey = $request->get('provider_key');
        $clientId = $clientLoader->create($providerKey, $context);
        $criteria = new Criteria();
        $criteria->setIds([$clientId]);
        $entity = $clientsRepository->search($criteria, $context)->first();

        return $responseFactory->createDetailResponse($criteria, $entity, $definition, $request, $context, false);
    }
}
