<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\StateFactory;

use Heptacom\AdminOpenAuth\Contract\StateFactory\ConfirmStateFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\Uuid\Uuid;

final class ConfirmStateFactory implements ConfirmStateFactoryInterface
{
    private EntityRepositoryInterface $loginsRepository;

    public function __construct(EntityRepositoryInterface $loginsRepository)
    {
        $this->loginsRepository = $loginsRepository;
    }

    public function create(string $clientId, string $userId, Context $context): string
    {
        $state = Uuid::randomHex();

        $this->loginsRepository->create([[
            'id' => Uuid::randomHex(),
            'clientId' => $clientId,
            'userId' => $userId,
            'state' => $state,
            'payload' => [
                'confirm' => true,
            ],
        ]], $context);

        return $state;
    }
}
