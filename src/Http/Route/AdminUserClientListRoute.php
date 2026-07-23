<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminUserClientListRoute extends AbstractController
{
    public function __construct(
        private readonly OpenAuthenticationFlowInterface $flow,
    ) {
    }

    #[Route(
        path: '/api/_admin/open-auth/client/list',
        name: 'api.heptacom.admin_open_auth.client.list',
        defaults: [
            '_routeScope' => ['administration'],
        ],
        methods: ['GET']
    )]
    public function clientList(Request $request, Context $context): Response
    {
        $userId = $request->query->getString('userId');

        if ($userId === '') {
            /** @var AdminApiSource $adminApiSource */
            $adminApiSource = $context->getSource();

            $userId = $adminApiSource->getUserId();
        }

        $criteria = new Criteria();
        $criteria->getAssociation('userKeys')
            ->addFilter(
                new EqualsFilter('userId', $userId)
            );

        $clients = $this->flow->getAvailableClients($criteria, $context)
            ->map(static fn (ClientEntity $client): array => [
                'id' => $client->getId(),
                'name' => $client->name,
                'connected' => $client->userKeys?->count() > 0,
            ]);

        return new JsonResponse([
            'data' => \array_values($clients),
        ]);
    }
}
