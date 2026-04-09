<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Contract\ModifiedRedirectBehaviourClientContract;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviourFactoryInterface;
use Heptacom\AdminOpenAuth\KskHeptacomAdminOpenAuth;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouterInterface;

final readonly class RedirectBehaviourFactoryService implements RedirectBehaviourFactoryInterface
{
    public function __construct(
        private SystemConfigService $systemConfigService,
        private ClientLoaderInterface $clientLoader,
        private RouterInterface $router,
        private string $appUrl,
    ) {
    }

    public function createRedirectBehaviour(string $clientId, Context $context): RedirectBehaviour
    {
        $client = $this->clientLoader->load($clientId, $context);

        $requestContext = $this->router->getContext();
        if ($this->systemConfigService->getBool(KskHeptacomAdminOpenAuth::CONFIG_ENABLE_UNIFIED_REDIRECT_DOMAIN)) {
            $modifiedContext = clone $requestContext;

            $unifiedDomain = $this->systemConfigService->getString(KskHeptacomAdminOpenAuth::CONFIG_UNIFIED_REDIRECT_DOMAIN);
            if ($unifiedDomain === '') {
                $unifiedDomain = $this->appUrl;
            }

            $unifiedDomainContext = RequestContext::fromUri($unifiedDomain);

            $modifiedContext->setHost($unifiedDomainContext->getHost());
            $modifiedContext->setScheme($unifiedDomainContext->getScheme());

            if ($modifiedContext->isSecure()) {
                $modifiedContext->setHttpsPort($unifiedDomainContext->getHttpsPort());
            } else {
                $modifiedContext->setHttpPort($unifiedDomainContext->getHttpPort());
            }

            $this->router->setContext($modifiedContext);
        }

        $behaviour = new RedirectBehaviour();
        $behaviour->expectState = true;
        $behaviour->redirectUri = $this->router->generate(
            'administration.heptacom.admin_open_auth.login',
            [ 'clientId' => $clientId ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $this->router->setContext($requestContext);

        if ($client instanceof ModifiedRedirectBehaviourClientContract) {
            $client->modifyRedirectBehaviour($behaviour);
        }

        return $behaviour;
    }
}
