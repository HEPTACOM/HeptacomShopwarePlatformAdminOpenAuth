<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Controller;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\MetadataClientContract;
use Heptacom\AdminOpenAuth\Database\ClientDefinition;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderRepositoryContract;
use Shopware\Core\Framework\Api\Response\ResponseFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * @RouteScope(scopes={"administration"})
 */
final class AdministrationController extends AbstractController
{
    public function __construct(
        private readonly ClientLoaderInterface $clientLoader,
        private readonly RouterInterface $router,
    ) {
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="api.heptacom.admin_open_auth.provider.metadata-url",
     *     path="/api/_action/heptacom_admin_open_auth_provider/client-metadata-url"
     * )
     */
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

    /**
     * @Route(
     *     methods={"GET"},
     *     name="api.heptacom.admin_open_auth.provider.list",
     *     path="/api/_action/heptacom_admin_open_auth_provider/list"
     * )
     */
    public function providerList(ClientProviderRepositoryContract $providerRepository): Response
    {
        return new JsonResponse([
            'data' => $providerRepository->getProviderKeys(),
        ]);
    }

    /**
     * @Route(
     *     methods={"POST"},
     *     name="api.heptacom.admin_open_auth.provider.factorize",
     *     path="/api/_action/heptacom_admin_open_auth_provider/factorize"
     * )
     */
    public function createClient(
        Request $request,
        ResponseFactoryInterface $responseFactory,
        ClientDefinition $definition,
        EntityRepository $clientsRepository,
        Context $context
    ): Response {
        $providerKey = $request->get('provider_key');
        $clientId = $this->clientLoader->create($providerKey, $context);
        $criteria = new Criteria();
        $criteria->setIds([$clientId]);
        $entity = $clientsRepository->search($criteria, $context)->first();

        return $responseFactory->createDetailResponse($criteria, $entity, $definition, $request, $context, false);
    }

    private function getSystemContext(Context $context): Context
    {
        return new Context(
            new SystemSource(),
            $context->getRuleIds(),
            $context->getCurrencyId(),
            $context->getLanguageIdChain(),
            $context->getVersionId(),
            $context->getCurrencyFactor(),
            $context->considerInheritance(),
            $context->getTaxState(),
            $context->getRounding()
        );
    }
}
