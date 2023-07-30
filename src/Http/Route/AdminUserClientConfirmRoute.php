<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route;

use Heptacom\AdminOpenAuth\Contract\AuthorizationUrl\LoginUrlGeneratorInterface;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviourFactoryInterface;
use Heptacom\AdminOpenAuth\Contract\StateFactory\ConfirmStateFactoryInterface;
use Shopware\Core\Framework\Api\Context\AdminApiSource;
use Shopware\Core\Framework\Context;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class AdminUserClientConfirmRoute extends AbstractController
{
    public function __construct(
        private readonly ConfirmStateFactoryInterface $confirmStateFactory,
        private readonly LoginUrlGeneratorInterface $loginUrlGenerator,
        private readonly RedirectBehaviourFactoryInterface $redirectBehaviourFactory,
    ) {
    }

    #[Route(
        path: '/api/_admin/open-auth/{clientId}/confirm',
        name: 'api.heptacom.admin_open_auth.confirm',
        defaults: [
            '_routeScope' => ['administration'],
        ],
        methods: ['GET']
    )]
    public function confirmUrl(string $clientId, Context $context): Response
    {
        /** @var AdminApiSource $adminApiSource */
        $adminApiSource = $context->getSource();

        $systemContext = $context->scope(Context::SYSTEM_SCOPE, static fn (Context $context): Context => $context);

        $state = $this->confirmStateFactory->create($clientId, $adminApiSource->getUserId(), $systemContext);

        return new JsonResponse([
            'target' => $this->loginUrlGenerator->generate(
                $clientId,
                $state,
                $this->redirectBehaviourFactory->createRedirectBehaviour($clientId, $context),
                $systemContext,
            ),
        ]);
    }
}
