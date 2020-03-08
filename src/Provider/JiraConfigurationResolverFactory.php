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
            'clientId',
            'clientSecret',
            'redirectUri',
            'scopes',
            'storeToken',
        ]);

        $result->setRequired([
            'clientId',
            'clientSecret',
            'redirectUri',
        ]);

        $result->setDefaults([
            'scopes' => [],
            'storeToken' => true,
        ]);

        $result->setAllowedTypes('clientId', 'string');
        $result->setAllowedTypes('clientSecret', 'string');
        $result->setAllowedTypes('redirectUri', 'string');
        $result->setAllowedTypes('scopes', 'array');
        $result->setAllowedTypes('storeToken', 'bool');

        return $result;
    }
}
