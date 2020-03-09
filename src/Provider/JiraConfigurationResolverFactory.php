<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Provider;

use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\ProviderConfigurationResolverFactoryInterface;
use Shopware\Core\Framework\Context;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JiraConfigurationResolverFactory implements ProviderConfigurationResolverFactoryInterface
{
    /**
     * @var ClientFeatureCheckerInterface
     */
    private $clientFeatureChecker;

    public function __construct(ClientFeatureCheckerInterface $clientFeatureChecker)
    {
        $this->clientFeatureChecker = $clientFeatureChecker;
    }

    public function getOptionResolver(string $clientId, Context $context): OptionsResolver
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

        $result->addNormalizer('scopes', function (Options $options, $value) use ($context, $clientId) {
            $scopes = (array) $value;

            if ($this->clientFeatureChecker->canStoreUserTokens($clientId, $context)) {
                $scopes[] = 'offline_access';
            }

            return array_unique(array_merge($scopes, [
                'read:me',
                'read:jira-user',
            ]));
        });

        return $result;
    }
}
