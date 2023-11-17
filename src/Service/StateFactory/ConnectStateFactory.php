<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\StateFactory;

use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\StateFactory\ConnectStateFactoryInterface;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;

final class ConnectStateFactory implements ConnectStateFactoryInterface
{
    public function __construct(
        private readonly EntityRepository $loginsRepository,
        private readonly ClientFeatureCheckerInterface $clientFeatureChecker,
    ) {
    }

    public function create(string $clientId, string $userId, string $redirectTo, Context $context): string
    {
        if (!$this->clientFeatureChecker->canConnect($clientId, $context)) {
            throw new LoadClientException('Client can not connect', $clientId, 1700229881);
        }

        $state = Uuid::randomHex();

        $this->loginsRepository->create([[
            'clientId' => $clientId,
            'userId' => $userId,
            'state' => $state,
            'type' => 'connect',
            'payload' => [
                'redirectTo' => $redirectTo,
            ],
        ]], $context);

        return $state;
    }
}
