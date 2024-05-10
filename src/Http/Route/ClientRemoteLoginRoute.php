<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviourFactoryInterface;
use Heptacom\AdminOpenAuth\Contract\StateFactory\LoginStateFactoryInterface;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ClientRemoteLoginRoute extends AbstractController
{
    public function __construct(
        private readonly RedirectBehaviourFactoryInterface $redirectBehaviourFactory,
        private readonly ClientLoaderInterface $clientLoader,
        private readonly LoginStateFactoryInterface $loginStateFactory,
    ) {
    }

    #[Route(
        path: '/admin/open-auth/{clientId}/remote',
        name: 'administration.heptacom.admin_open_auth.remote_login',
        defaults: [
            'auth_required' => false,
            '_routeScope' => ['administration'],
        ],
        methods: ['GET']
    )]
    public function remoteLogin(
        string $clientId,
        Request $request,
        Context $context
    ): Response {
        $redirectTo = (string) $request->query->get('redirectTo') ?: null;

        if ($redirectTo && !\str_starts_with($redirectTo, '/')) {
            throw new BadRequestException('Only absolute redirect urls are allowed.');
        }

        $systemContext = $context->scope(Context::SYSTEM_SCOPE, static fn (Context $context): Context => $context);
        $state = $this->loginStateFactory->create($clientId, $redirectTo, $systemContext);
        $redirectBehaviour = $this->redirectBehaviourFactory->createRedirectBehaviour($clientId, $context);
        $target = $this->clientLoader->load($clientId, $context)->getLoginUrl($state, $redirectBehaviour);

        return new RedirectResponse($target, Response::HTTP_TEMPORARY_REDIRECT);
    }
}
