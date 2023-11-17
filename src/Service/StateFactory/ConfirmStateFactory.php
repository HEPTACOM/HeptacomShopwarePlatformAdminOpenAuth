<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\StateFactory;

use Heptacom\AdminOpenAuth\Contract\StateFactory\ConfirmStateFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class ConfirmStateFactory implements ConfirmStateFactoryInterface
{
    public function __construct(
        private readonly EntityRepository $loginsRepository,
        private readonly RouterInterface $router
    ) {
    }

    public function create(string $clientId, string $userId, Context $context): string
    {
        $state = Uuid::randomHex();

        $this->loginsRepository->create([[
            'id' => Uuid::randomHex(),
            'clientId' => $clientId,
            'userId' => $userId,
            'state' => $state,
            'type' => 'login',
            'payload' => [
                'redirectTo' => $this->router->generate(
                    'administration.heptacom.admin_open_auth.confirm',
                    [],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
            ],
        ]], $context);

        return $state;
    }
}
