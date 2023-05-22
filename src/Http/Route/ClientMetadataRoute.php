<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\MetadataClientContract;
use Heptacom\AdminOpenAuth\Exception\LoadClientClientNotFoundException;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

final class ClientMetadataRoute extends AbstractController
{
    public function __construct(
        private readonly ClientLoaderInterface $clientLoader,
    ) {
    }

    #[Route(
        path: '/admin/open-auth/{clientId}/metadata',
        name: 'administration.heptacom.admin_open_auth.metadata',
        defaults: [
            'auth_required' => false,
            '_routeScope' => ['administration']
        ],
        methods: ['GET']
    )]
    public function metadata(string $clientId, Context $context): Response
    {
        try {
            $clientProvider = $this->clientLoader->load($clientId, $context);

            if (!$clientProvider instanceof MetadataClientContract) {
                throw new BadRequestException();
            }

            $fileType = $clientProvider->getMetadataType();
            $fileExtension = match ($fileType) {
                'application/json' => '.json',
                'application/xml' => '.xml',
                default => '',
            };
            $fileName = 'metadata_' . $clientId . $fileExtension;

            $response = new Response();
            $response->headers->add([
                'Content-Type' => $fileType,
                'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
            ]);
            $response->setContent($clientProvider->getMetadata());

            return $response;
        } catch (LoadClientClientNotFoundException) {
            throw new NotFoundHttpException();
        }
    }
}
