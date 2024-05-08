<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Database\ClientDefinition;
use Shopware\Core\Framework\Api\Response\ResponseFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminUserClientCreateRoute extends AbstractController
{
    public function __construct(
        private readonly ClientLoaderInterface $clientLoader,
        private readonly ClientDefinition $definition,
        private readonly EntityRepository $clientsRepository,
    ) {
    }

    #[Route(
        path: '/api/_action/heptacom_admin_open_auth_provider/factorize',
        name: 'api.heptacom.admin_open_auth.provider.factorize',
        defaults: [
            '_routeScope' => ['administration'],
        ],
        methods: ['POST']
    )]
    public function createClient(Request $request, ResponseFactoryInterface $responseFactory, Context $context): Response
    {
        $providerKey = $request->get('provider_key');
        $clientId = $this->clientLoader->create($providerKey, $context);
        $criteria = new Criteria();
        $criteria->setIds([$clientId]);
        $entity = $this->clientsRepository->search($criteria, $context)->first();

        return $responseFactory->createDetailResponse(
            $criteria,
            $entity,
            $this->definition,
            $request,
            $context,
            false
        );
    }
}
