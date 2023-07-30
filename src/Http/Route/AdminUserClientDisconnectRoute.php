<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\OpenAuthenticationFlowInterface;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUserClientDisconnectRoute extends AbstractController
{
    public function __construct(
        private readonly OpenAuthenticationFlowInterface $flow,
    ) {
    }

    #[Route(
        path: '/api/_action/open-auth/{clientId}/disconnect',
        name: 'api.heptacom.admin_open_auth.remote_disconnect',
        defaults: [
            '_acl' => ['user_change_me'],
            '_routeScope' => ['administration'],
        ],
        methods: ['POST']
    )]
    public function remoteDisconnect(string $clientId, Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        $this->flow->disconnectClient(
            $clientId,
            $adminApiSource->getUserId(),
            $context->scope(Context::SYSTEM_SCOPE, static fn (Context $context): Context => $context),
        );

        return new JsonResponse();
    }
}
