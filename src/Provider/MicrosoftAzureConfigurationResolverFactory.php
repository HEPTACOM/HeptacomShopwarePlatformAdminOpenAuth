<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Provider;

use Heptacom\AdminOpenAuth\Contract\ProviderConfigurationResolverFactoryInterface;
use Shopware\Core\Framework\Context;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MicrosoftAzureConfigurationResolverFactory implements ProviderConfigurationResolverFactoryInterface
{
    public function getOptionResolver(string $clientId, Context $context): OptionsResolver
    {
        $result = new OptionsResolver();
        $result->setDefined([
            'clientId',
            'clientSecret',
            'redirectUri',
            'scopes',
        ]);

        $result->setRequired([
            'clientId',
            'clientSecret',
            'redirectUri',
        ]);

        $result->setDefaults([
            'scopes' => [],
        ]);

        $result->setAllowedTypes('clientId', 'string');
        $result->setAllowedTypes('clientSecret', 'string');
        $result->setAllowedTypes('redirectUri', 'string');
        $result->setAllowedTypes('scopes', 'array');

        return $result;
    }
}
