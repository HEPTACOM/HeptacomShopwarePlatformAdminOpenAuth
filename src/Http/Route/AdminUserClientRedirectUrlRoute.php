<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class AdminUserClientRedirectUrlRoute extends AbstractController
{
    public function __construct(
        private readonly RouterInterface $router,
    ) {
    }

    #[Route(
        path: '/api/_action/heptacom_admin_open_auth_provider/client-redirect-url',
        name: 'api.heptacom.admin_open_auth.provider.redirect-url',
        defaults: [
            '_routeScope' => ['administration'],
        ],
        methods: ['POST']
    )]
    public function getRedirectUrl(Request $request): Response
    {
        $clientId = $request->get('client_id');

        return new JsonResponse([
            'target' => $this->router->generate('administration.heptacom.admin_open_auth.login', [
                'clientId' => $clientId,
            ], UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }
}
