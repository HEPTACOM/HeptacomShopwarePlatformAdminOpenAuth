<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviourFactoryInterface;
use Heptacom\AdminOpenAuth\Database\ClientCollection;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Heptacom\AdminOpenAuth\Http\Route\Support\BeforeUserRedirectEvent;
use Heptacom\AdminOpenAuth\Http\Route\Support\RedirectReceiveRoute;
use Heptacom\AdminOpenAuth\Http\Route\Support\UserRedirectAuthenticationEvent;
use Heptacom\AdminOpenAuth\Service\StateResolver;
use Nyholm\Psr7\Factory\Psr17Factory;
use Sentry\SentrySdk;
use Sentry\State\Scope;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\Framework\Feature;
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
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use function Sentry\captureMessage;
use function Sentry\withScope;

final class ClientRedirectRoute extends AbstractController
{
    public const FEATURE_HEPTACOM_OPEN_AUTH_SSO_LOG_ATTEMPTS_TO_SENTRY = 'HEPTACOM_OPEN_AUTH_SSO_LOG_ATTEMPTS_TO_SENTRY';

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

        if (Feature::isActive(self::FEATURE_HEPTACOM_OPEN_AUTH_SSO_LOG_ATTEMPTS_TO_SENTRY) && class_exists(SentrySdk::class)) {
            withScope(function (Scope $scope) use ($request): void {
                $scope->setContext('saml', [
                    'response' => base64_decode($request->request->get('SAMLResponse'))
                ]);
                captureMessage('SSO login attempt');
            });
        }

        $user = $this->redirectReceiveRoute
            ->onReceiveRequest(
                $psrHttpFactory->createRequest($request),
                $client,
                $this->redirectBehaviourFactory->createRedirectBehaviour($clientId, $context),
                $client->rules,
                $context
            );

        /** @var ArrayStruct|null $requestStateExtension */
        $requestStateExtension = $user->getExtensionOfType('requestState', ArrayStruct::class);
        $requestState = (string) $requestStateExtension?->offsetGet('requestState');
        $salesChannelId = $requestStateExtension?->offsetGet('salesChannelId');

        $userId = $this->flow->upsertUser($user, $clientId, $requestState, $context);

        $this->eventDispatcher->dispatch(
            new UserRedirectAuthenticationEvent(
                $user,
                $client
            )
        );

        $statePayload = $this->stateResolver->getPayload($requestState, $context);

        $targetUrl = $statePayload['redirectTo'] ??
            $salesChannelId
            ? $this->generateUrl(
                'frontend.home.page',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            )
            : $this->generateUrl(
                'administration.index',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            );
        $targetUrl = $this->enrichRedirectUrl($targetUrl, $requestState);

        $this->eventDispatcher->dispatch(new BeforeUserRedirectEvent($userId, $user, $clientId, $statePayload));

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
