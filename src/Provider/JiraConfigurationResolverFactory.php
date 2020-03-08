<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Provider;

use Heptacom\AdminOpenAuth\Contract\ProviderConfigurationResolverFactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JiraConfigurationResolverFactory implements ProviderConfigurationResolverFactoryInterface
{
    public function getOptionResolver(): OptionsResolver
    {
        $result = new OptionsResolver();
        $result->setDefined([
            'appId',
            'appSecret',
            'redirectUri',
            'scopes',
            'storeToken',

        ]);

        $result->setRequired([
            'appId',
            'appSecret',
            'redirectUri',
        ]);

        $result->setDefaults([
            'scopes' => [],
            'storeToken' => true,
        ]);

        $result->setAllowedTypes('appId', 'string');
        $result->setAllowedTypes('appSecret', 'string');
        $result->setAllowedTypes('redirectUri', 'string');
        $result->setAllowedTypes('scopes', 'array');
        $result->setAllowedTypes('storeToken', 'bool');

        return $result;
    }
}
