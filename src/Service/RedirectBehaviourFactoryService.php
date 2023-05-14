<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\ModifiedRedirectBehaviourClientContract;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviourFactoryInterface;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Shopware\Core\Framework\Context;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

final class RedirectBehaviourFactoryService implements RedirectBehaviourFactoryInterface
{
    public function __construct(private readonly ClientLoaderInterface $clientLoader, private readonly RouterInterface $router)
    {
    }

    public function createRedirectBehaviour(string $clientId, Context $context): RedirectBehaviour
    {
        $client = $this->clientLoader->load($clientId, $context);

        $behaviour = (new RedirectBehaviour())
            ->setExpectState(true)
            ->setRedirectUri($this->router->generate('administration.heptacom.admin_open_auth.login', [
                'clientId' => $clientId,
            ], UrlGeneratorInterface::ABSOLUTE_URL));

        if ($client instanceof ModifiedRedirectBehaviourClientContract) {
            $client->modifyRedirectBehaviour($behaviour);
        }

        return $behaviour;
    }
}