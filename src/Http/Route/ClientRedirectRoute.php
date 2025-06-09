<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviourFactoryInterface;
use Heptacom\AdminOpenAuth\Database\ClientCollection;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\AdminOpenAuth\Http\Route\Support\RedirectReceiveRoute;
use Heptacom\AdminOpenAuth\Http\Route\Support\UserRedirectAuthenticationEvent;
use Heptacom\AdminOpenAuth\Service\StateResolver;
use Nyholm\Psr7\Factory\Psr17Factory;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Struct\ArrayStruct;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class ClientRedirectRoute extends AbstractController
{
    /**
     * @param EntityRepository<ClientCollection> $clientsRepository
     */
    public function __construct(
        private readonly OpenAuthenticationFlowInterface $flow,
        private readonly EntityRepository $clientsRepository,
        private readonly RedirectReceiveRoute $redirectReceiveRoute,
        private readonly RedirectBehaviourFactoryInterface $redirectBehaviourFactory,
        private readonly StateResolver $stateResolver,
        private readonly EventDispatcherInterface $eventDispatcher,
    ) {
    }

    #[Route(
        path: '/admin/open-auth/{clientId}/redirect',
        name: 'administration.heptacom.admin_open_auth.login',
        defaults: [
            'auth_required' => false,
            '_routeScope' => ['administration'],
        ],
        methods: ['GET', 'POST']
    )]
    public function login(string $clientId, Request $request, Context $context): Response
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);

        $clientCriteria = new Criteria([$clientId]);
        $clientCriteria->getAssociation('rules')->addSorting(new FieldSorting('position', FieldSorting::ASCENDING));

        /** @var ClientEntity|null $client */
        $client = $this->clientsRepository->search($clientCriteria, $context)->first();

        if (!$client instanceof ClientEntity) {
            throw new NotFoundHttpException();
        }

        $user = $this->redirectReceiveRoute
            ->onReceiveRequest(
                $psrHttpFactory->createRequest($request),
                $client,
                $this->redirectBehaviourFactory->createRedirectBehaviour($clientId, $context),
            );
        $requestState = (string) $user->getExtensionOfType('requestState', ArrayStruct::class)['requestState'];

        $this->flow->upsertUser($user, $clientId, $requestState, $context);

        $this->eventDispatcher->dispatch(
            new UserRedirectAuthenticationEvent(
                $user,
                $client
            )
        );

        $statePayload = $this->stateResolver->getPayload($requestState, $context);
        $targetUrl = $statePayload['redirectTo'] ?? $this->generateUrl(
            'administration.index',
            [],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        $targetUrl = $this->enrichRedirectUrl($targetUrl, $requestState);

        // redirect with "303 See Other" to ensure the request method becomes GET
        return new RedirectResponse($targetUrl, Response::HTTP_SEE_OTHER);
    }

    protected function enrichRedirectUrl(string $targetUrl, string $requestState): string
    {
        $targetUrlParts = [
            ...[
                'path' => '/',
                'query' => '',
                'fragment' => '',
            ],
            ...\parse_url($targetUrl),
        ];

        $targetUrl = '';

        if (\array_key_exists('scheme', $targetUrlParts) && \array_key_exists('host', $targetUrlParts)) {
            $targetUrl .= $targetUrlParts['scheme'] . '://' . $targetUrlParts['host'];
        }

        if (\array_key_exists('port', $targetUrlParts)) {
            $targetUrl .= ':' . $targetUrlParts['port'];
        }

        $targetUrl .= $targetUrlParts['path']
            . '?' . \ltrim($targetUrlParts['query'] . '&state=' . \urlencode($requestState), '&')
            . '#' . $targetUrlParts['fragment'];

        return $targetUrl;
    }
}
