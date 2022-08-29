<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Controller;

use Heptacom\AdminOpenAuth\Contract\AuthorizationUrl\LoginUrlGeneratorInterface;
use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Heptacom\AdminOpenAuth\Contract\StateFactory\ConfirmStateFactoryInterface;
use Heptacom\AdminOpenAuth\Database\ClientDefinition;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\AdminOpenAuth\Service\StateResolver;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderRepositoryContract;
use Heptacom\OpenAuth\Route\Contract\RedirectReceiveRouteContract;
use Nyholm\Psr7\Factory\Psr17Factory;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Api\Context\SystemSource;
use Shopware\Core\Framework\Api\Response\ResponseFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Shopware\Core\PlatformRequest;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @RouteScope(scopes={"administration"})
 */
class AdministrationController extends AbstractController
{
    private OpenAuthenticationFlowInterface $flow;

    private EntityRepositoryInterface $clientsRepository;

    private RedirectReceiveRouteContract $redirectReceiveRoute;

    private ConfirmStateFactoryInterface $confirmStateFactory;

    private LoginUrlGeneratorInterface $loginUrlGenerator;

    private RouterInterface $router;

    private StateResolver $stateResolver;

    public function __construct(
        OpenAuthenticationFlowInterface $flow,
        EntityRepositoryInterface $clientsRepository,
        RedirectReceiveRouteContract $redirectReceiveRoute,
        ConfirmStateFactoryInterface $confirmStateFactory,
        LoginUrlGeneratorInterface $confirmationUrlGenerator,
        RouterInterface $router,
        StateResolver $stateResolver
    ) {
        $this->flow = $flow;
        $this->clientsRepository = $clientsRepository;
        $this->redirectReceiveRoute = $redirectReceiveRoute;
        $this->confirmStateFactory = $confirmStateFactory;
        $this->loginUrlGenerator = $confirmationUrlGenerator;
        $this->router = $router;
        $this->stateResolver = $stateResolver;
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
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        /** @var ClientEntity|null $client */
        $client = $this->clientsRepository->search(new Criteria([$clientId]), $context)->first();

        if (!$client instanceof ClientEntity) {
            // TODO handle exceptions
        }

        $user = $this->redirectReceiveRoute
            ->onReceiveRequest(
                $psrHttpFactory->createRequest($request),
                $client->getProvider(),
                $client->getConfig(),
                $this->getRedirectBehaviour($clientId)
            );
        $requestState = (string) $user->getPassthrough()['requestState'];

        $this->flow->upsertUser($user, $clientId, $requestState, $context);

        $statePayload = $this->stateResolver->getPayload($requestState, $context);
        $targetRoute = 'administration.index';

        if ($statePayload['confirm'] ?? false) {
            $targetRoute = 'administration.heptacom.admin_open_auth.confirm';
        }

        $targetUrl = $this->generateUrl(
            $targetRoute,
            ['state' => $requestState],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        return new RedirectResponse($targetUrl, Response::HTTP_TEMPORARY_REDIRECT);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="administration.heptacom.admin_open_auth.confirm",
     *     path="/admin/open-auth/confirm",
     *     defaults={"auth_required" = false}
     * )
     */
    public function confirm(Request $request): Response
    {
        return $this->render('@KskHeptacomAdminOpenAuth/administration/heptacom-admin-open-auth/page/confirm.html.twig', [
            'cspNonce' => $request->attributes->get(PlatformRequest::ATTRIBUTE_CSP_NONCE),
        ]);
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
     *     name="api.heptacom.admin_open_auth.client.list",
     *     path="/api/_action/open-auth/client/list"
     * )
     */
    public function clientList(Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        $criteria = new Criteria();
        $criteria->getAssociation('userKeys')
            ->addFilter(
                new EqualsFilter('userId', $adminApiSource->getUserId())
            );

        $clients = $this->flow->getAvailableClients($criteria, $context)
            ->map(static fn (ClientEntity $client): array => [
                'id' => $client->getId(),
                'name' => $client->getName(),
                'connected' => $client->getUserKeys()->count() > 0,
            ]);

        return JsonResponse::create([
            'data' => \array_values($clients),
        ]);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="api.heptacom.admin_open_auth.remote_connect",
     *     path="/api/_action/open-auth/{clientId}/connect"
     * )
     */
    public function remoteConnect(string $clientId, Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        return JsonResponse::create([
            'target' => $this->flow->getRedirectUrlToConnect(
                $clientId,
                $adminApiSource->getUserId(),
                $this->getSystemContext($context)
            ),
        ]);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="api.heptacom.admin_open_auth.remote_disconnect",
     *     path="/api/_action/open-auth/{clientId}/disconnect"
     * )
     */
    public function remoteDisconnect(string $clientId, Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        $this->flow->disconnectClient($clientId, $adminApiSource->getUserId(), $this->getSystemContext($context));

        return JsonResponse::create([]);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="api.heptacom.admin_open_auth.confirm",
     *     path="/api/_action/open-auth/{clientId}/confirm"
     * )
     */
    public function confirmUrl(string $clientId, Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        $systemContext = $this->getSystemContext($context);

        $state = $this->confirmStateFactory->create($clientId, $adminApiSource->getUserId(), $systemContext);

        return JsonResponse::create([
            'target' => $this->loginUrlGenerator->generate(
                $clientId,
                $state,
                $this->getRedirectBehaviour($clientId),
                $systemContext
            ),
        ]);
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="api.heptacom.admin_open_auth.provider.redirect-url",
     *     path="/api/_action/heptacom_admin_open_auth_provider/client-redirect-url"
     * )
     */
    public function getRedirectUrl(Request $request): Response
    {
        $clientId = $request->get('client_id');

        return JsonResponse::create([
            'target' => $this->generateRedirectUrl($clientId),
        ]);
    }

    /**
     * @Route(
     *     methods={"GET"},
     *     name="api.heptacom.admin_open_auth.provider.list",
     *     path="/api/_action/heptacom_admin_open_auth_provider/list"
     * )
     */
    public function providerList(ClientProviderRepositoryContract $providerRepository): Response
    {
        return JsonResponse::create([
            'data' => $providerRepository->getProviderKeys(),
        ]);
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="api.heptacom.admin_open_auth.provider.factorize",
     *     path="/api/_action/heptacom_admin_open_auth_provider/factorize"
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

    private function generateRedirectUrl(string $clientId): string
    {
        return $this->router->generate('administration.heptacom.admin_open_auth.login', [
            'clientId' => $clientId,
        ], UrlGeneratorInterface::ABSOLUTE_URL);
    }

    private function getRedirectBehaviour(string $clientId): RedirectBehaviour
    {
        return (new RedirectBehaviour())
            ->setExpectState(true)
            ->setRedirectUri($this->generateRedirectUrl($clientId));
    }

    private function getSystemContext(Context $context): Context
    {
        return new Context(
            new SystemSource(),
            $context->getRuleIds(),
            $context->getCurrencyId(),
            $context->getLanguageIdChain(),
            $context->getVersionId(),
            $context->getCurrencyFactor(),
            $context->considerInheritance(),
            $context->getTaxState(),
            $context->getRounding()
        );
    }
}
