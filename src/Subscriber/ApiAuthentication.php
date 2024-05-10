<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Subscriber;

use Heptacom\AdminOpenAuth\Component\OpenAuth\OneTimeTokenGrant;
use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final readonly class ApiAuthentication implements EventSubscriberInterface
{
    public function __construct(
        private AuthorizationServer $authorizationServer,
        private UserRepositoryInterface $userRepository,
        private RefreshTokenRepositoryInterface $refreshTokenRepository,
        private LoginInterface $login,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => [
                ['addOneTimeTokenGrant', 128],
            ],
        ];
    }

    public function addOneTimeTokenGrant(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $tenMinuteInterval = new \DateInterval('PT10M');
        $oneWeekInterval = new \DateInterval('P1W');

        $passwordGrant = new OneTimeTokenGrant($this->userRepository, $this->refreshTokenRepository, $this->login);
        $passwordGrant->setRefreshTokenTTL($oneWeekInterval);

        $this->authorizationServer->enableGrantType($passwordGrant, $tenMinuteInterval);
    }
}
