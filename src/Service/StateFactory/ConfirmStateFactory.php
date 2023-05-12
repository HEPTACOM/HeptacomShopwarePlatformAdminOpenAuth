<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\StateFactory;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Heptacom\AdminOpenAuth\Contract\StateFactory\ConfirmStateFactoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\Uuid\Uuid;

final class ConfirmStateFactory implements ConfirmStateFactoryInterface
{
    public function __construct(private readonly EntityRepository $loginsRepository)
    {
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
