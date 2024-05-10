<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Shopware\Core\PlatformRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ConfirmationMessageRoute extends AbstractController
{
    #[Route(
        path: '/admin/open-auth/confirm',
        name: 'administration.heptacom.admin_open_auth.confirm',
        defaults: [
            'auth_required' => false,
            '_routeScope' => ['administration'],
        ],
        methods: ['GET']
    )]
    public function confirm(Request $request): Response
    {
        return $this->render('@KskHeptacomAdminOpenAuth/administration/heptacom-admin-open-auth/page/confirm.html.twig', [
            'cspNonce' => $request->attributes->get(PlatformRequest::ATTRIBUTE_CSP_NONCE),
        ]);
    }
}
