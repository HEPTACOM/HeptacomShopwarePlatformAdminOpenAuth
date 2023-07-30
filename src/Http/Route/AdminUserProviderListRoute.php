<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\ClientProvider\ClientProviderRepositoryContract;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUserProviderListRoute extends AbstractController
{
    public function __construct(
        private readonly ClientProviderRepositoryContract $providerRepository,
    ) {
    }

    #[Route(
        path: '/api/_action/heptacom_admin_open_auth_provider/list',
        name: 'api.heptacom.admin_open_auth.provider.list',
        defaults: [
            '_routeScope' => ['administration'],
        ],
        methods: ['GET']
    )]
    public function providerList(): Response
    {
        return new JsonResponse([
            'data' => $this->providerRepository->getProviderKeys(),
        ]);
    }
}
