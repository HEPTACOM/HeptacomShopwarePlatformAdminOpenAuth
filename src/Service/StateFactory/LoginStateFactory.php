<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\StateFactory;

use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\StateFactory\LoginStateFactoryInterface;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;

final class LoginStateFactory implements LoginStateFactoryInterface
{
    public function __construct(
        private readonly EntityRepository $loginsRepository,
        private readonly ClientFeatureCheckerInterface $clientFeatureChecker,
    ) {
    }

    public function create(string $clientId, string $redirectTo, Context $context): string
    {
        if (!$this->clientFeatureChecker->canLogin($clientId, $context)) {
            throw new LoadClientException('Client can not login', $clientId, 1700229880);
        }

        $state = Uuid::randomHex();

        $this->loginsRepository->create([[
            'clientId' => $clientId,
            'userId' => null,
            'type' => 'login',
            'state' => $state,
            'payload' => [
                'redirectTo' => $redirectTo,
            ],
        ]], $context);

        return $state;
    }
}
