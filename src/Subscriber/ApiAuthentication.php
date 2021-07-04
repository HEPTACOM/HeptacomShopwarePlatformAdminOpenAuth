<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Subscriber;

use Heptacom\AdminOpenAuth\Component\OpenAuth\OneTimeTokenGrant;
use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ApiAuthentication implements EventSubscriberInterface
{
    private AuthorizationServer $authorizationServer;

    private UserRepositoryInterface $userRepository;

    private RefreshTokenRepositoryInterface $refreshTokenRepository;

    private LoginInterface $login;

    public function __construct(
        AuthorizationServer $authorizationServer,
        UserRepositoryInterface $userRepository,
        RefreshTokenRepositoryInterface $refreshTokenRepository,
        LoginInterface $login
    ) {
        $this->authorizationServer = $authorizationServer;
        $this->userRepository = $userRepository;
        $this->refreshTokenRepository = $refreshTokenRepository;
        $this->login = $login;
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
        if (!$event->isMasterRequest()) {
            return;
        }

        $tenMinuteInterval = new \DateInterval('PT10M');
        $oneWeekInterval = new \DateInterval('P1W');

        $passwordGrant = new OneTimeTokenGrant($this->userRepository, $this->refreshTokenRepository, $this->login);
        $passwordGrant->setRefreshTokenTTL($oneWeekInterval);

        $this->authorizationServer->enableGrantType($passwordGrant, $tenMinuteInterval);
    }
}
