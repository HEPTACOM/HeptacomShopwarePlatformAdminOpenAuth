<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\StateFactory;

use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\StateFactory\ConfirmStateFactoryInterface;
use Heptacom\AdminOpenAuth\Database\LoginCollection;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final readonly class ConfirmStateFactory implements ConfirmStateFactoryInterface
{
    /**
     * @param EntityRepository<LoginCollection> $loginsRepository
     */
    public function __construct(
        private EntityRepository $loginsRepository,
        private RouterInterface $router,
        private ClientFeatureCheckerInterface $clientFeatureChecker,
    ) {
    }

    public function create(string $clientId, string $userId, Context $context): string
    {
        if (!$this->clientFeatureChecker->canLogin($clientId, $context)) {
            throw new LoadClientException('Client can not login', $clientId, 1700229882);
        }

        $state = Uuid::randomHex();

        $this->loginsRepository->create([[
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
