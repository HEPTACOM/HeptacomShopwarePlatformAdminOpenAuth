<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Saml\Saml2ServiceProviderService;
use Symfony\Component\Routing\RouterInterface;

class JumpCloudServiceProvider extends Saml2ServiceProvider
{
    public const PROVIDER_NAME = 'jumpcloud';

    public function __construct(
        string $appSecret,
        Saml2ServiceProviderService $saml2ServiceProviderService,
        RouterInterface $router
    ) {
        parent::__construct($appSecret, $saml2ServiceProviderService, $router);
    }

    public function provides(): string
    {
        return static::PROVIDER_NAME;
    }

    public function getInitialConfiguration(): array
    {
        $config = parent::getInitialConfiguration();
        $config['attributeMapping'] = array_merge($config['attributeMapping'], [
            'firstName' => 'firstname',
            'lastName' => 'lastname',
            'email' => 'email',
        ]);

        return $config;
    }

}
