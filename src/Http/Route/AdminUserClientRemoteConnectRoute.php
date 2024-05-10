<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviourFactoryInterface;
use Heptacom\AdminOpenAuth\Contract\StateFactory\ConnectStateFactoryInterface;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminUserClientRemoteConnectRoute extends AbstractController
{
    public function __construct(
        private readonly ConnectStateFactoryInterface $connectStateFactory,
        private readonly ClientLoaderInterface $clientLoader,
        private readonly RedirectBehaviourFactoryInterface $redirectBehaviourFactory,
    ) {
    }

    #[Route(
        path: '/api/_action/open-auth/{clientId}/connect',
        name: 'api.heptacom.admin_open_auth.remote_connect',
        defaults: [
            '_acl' => ['user_change_me'],
            '_routeScope' => ['administration'],
        ],
        methods: ['POST']
    )]
    public function remoteConnect(string $clientId, Request $request, Context $context): Response
    {
        $redirectTo = (string) $request->query->get('redirectTo') ?: null;

        if ($redirectTo && !\str_starts_with($redirectTo, '/')) {
            throw new BadRequestException('Only absolute redirect urls are allowed.');
        }

        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();
        $systemContext = $context->scope(Context::SYSTEM_SCOPE, static fn (Context $context): Context => $context);
        $state = $this->connectStateFactory->create($clientId, $adminApiSource->getUserId(), $redirectTo, $systemContext);
        $redirectBehaviour = $this->redirectBehaviourFactory->createRedirectBehaviour($clientId, $context);
        $target = $this->clientLoader->load($clientId, $context)->getLoginUrl($state, $redirectBehaviour);

        return new JsonResponse([
            'target' => $target,
        ]);
    }
}
