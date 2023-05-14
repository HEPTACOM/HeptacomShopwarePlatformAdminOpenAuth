<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviourFactoryInterface;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\AdminOpenAuth\OpenAuth\Struct\UserStructExtension;
use Heptacom\AdminOpenAuth\Service\StateResolver;
use Heptacom\OpenAuth\Route\Contract\RedirectReceiveRouteContract;
use Nyholm\Psr7\Factory\Psr17Factory;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ClientRedirectRoute extends AbstractController
{
    public function __construct(
        private readonly OpenAuthenticationFlowInterface $flow,
        private readonly EntityRepository $clientsRepository,
        private readonly RedirectReceiveRouteContract $redirectReceiveRoute,
        private readonly RedirectBehaviourFactoryInterface $redirectBehaviourFactory,
        private readonly StateResolver $stateResolver,
    ) {
    }

    #[Route(
        path: '/admin/open-auth/{clientId}/redirect',
        name: 'administration.heptacom.admin_open_auth.login',
        defaults: [
            'auth_required' => false,
            '_routeScope' => ['administration']
        ],
        methods: ['GET', 'POST']
    )]
    public function login(string $clientId, Request $request, Context $context): Response
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        $clientCriteria = new Criteria([$clientId]);
        $clientCriteria->addAssociation('defaultAclRoles');
        /** @var ClientEntity|null $client */
        $client = $this->clientsRepository->search($clientCriteria, $context)->first();

        if (!$client instanceof ClientEntity) {
            throw new NotFoundHttpException();
        }

        $user = $this->redirectReceiveRoute
            ->onReceiveRequest(
                $psrHttpFactory->createRequest($request),
                $client->provider,
                $client->config,
                $this->redirectBehaviourFactory->createRedirectBehaviour($clientId, $context)
            );
        $requestState = (string) $user->getPassthrough()['requestState'];

        $userExtension = $user->getPassthrough()[UserStructExtension::class] ?? new UserStructExtension();
        $userExtension->setIsAdmin($client->userBecomeAdmin ?? false);
        $userExtension->setAclRoleIds($client->defaultAclRoles?->getIds() ?? []);
        $user->addPassthrough(UserStructExtension::class, $userExtension);

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

        // redirect with "303 See Other" to ensure the request method becomes GET
        return new RedirectResponse($targetUrl, Response::HTTP_SEE_OTHER);
    }
}