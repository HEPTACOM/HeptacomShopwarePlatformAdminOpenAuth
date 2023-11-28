<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\MetadataClientContract;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class AdminUserClientMetadataUrlRoute extends AbstractController
{
    public function __construct(
        private readonly ClientLoaderInterface $clientLoader,
        private readonly RouterInterface $router,
    ) {
    }

    #[Route(
        path: '/api/_action/heptacom_admin_open_auth_provider/client-metadata-url',
        name: 'api.heptacom.admin_open_auth.provider.metadata-url',
        defaults: [
            '_routeScope' => ['administration'],
        ],
        methods: ['POST']
    )]
    public function getMetadataUrl(Request $request, Context $context): Response
    {
        $clientId = $request->get('client_id');

        $client = $this->clientLoader->load($clientId, $context);
        if ($client instanceof MetadataClientContract) {
            $metadataUrl = $this->router->generate('administration.heptacom.admin_open_auth.metadata', [
                'clientId' => $clientId,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
        } else {
            $metadataUrl = null;
        }

        return new JsonResponse([
            'target' => $metadataUrl,
        ]);
    }
}
