<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class ClientRemoteLoginRoute extends AbstractController
{
    public function __construct(
        private readonly OpenAuthenticationFlowInterface $flow,
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

        return new RedirectResponse(
            $this->flow->getRedirectUrl($clientId, $redirectTo, $context),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
