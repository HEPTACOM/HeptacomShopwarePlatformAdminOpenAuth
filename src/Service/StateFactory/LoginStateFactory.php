<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\StateFactory;

use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\StateFactory\LoginStateFactoryInterface;
use Heptacom\AdminOpenAuth\Database\LoginCollection;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;

final readonly class LoginStateFactory implements LoginStateFactoryInterface
{
    /**
     * @param EntityRepository<LoginCollection> $loginsRepository
     */
    public function __construct(
        private EntityRepository $loginsRepository,
        private ClientFeatureCheckerInterface $clientFeatureChecker,
    ) {
    }

    public function create(string $clientId, ?string $redirectTo, Context $context): string
    {
        return $this->createWithPayload($clientId, $redirectTo, [], $context);
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function createWithPayload(string $clientId, ?string $redirectTo, array $payload, Context $context): string
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
            // keys owned by the plugin are applied last, so a caller cannot redefine them
            'payload' => [
                ...$payload,
                'redirectTo' => $redirectTo,
            ],
        ]], $context);

        return $state;
    }
}
