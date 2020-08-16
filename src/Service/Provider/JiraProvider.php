<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\AdminOpenAuth\Component\Provider\JiraClient;
use Heptacom\OpenAuth\Client\Contract\TokenPairFactoryContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JiraProvider extends ClientProviderContract
{
    /**
     * @var TokenPairFactoryContract
     */
    private $tokenPairFactory;

    public function __construct(TokenPairFactoryContract $tokenPairFactory)
    {
        $this->tokenPairFactory = $tokenPairFactory;
    }

    public function provides(): string
    {
        return 'jira';
    }

    public function getConfigurationTemplate(): OptionsResolver
    {
        return parent::getConfigurationTemplate()
            ->setDefined([
                'clientId',
                'clientSecret',
                'redirectUri',
                'scopes',
            ])->setRequired([
                'clientId',
                'clientSecret',
                'redirectUri',
            ])->setDefaults([
                'scopes' => [],
            ])->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('redirectUri', 'string')
            ->setAllowedTypes('scopes', 'array')
            ->addNormalizer('scopes', static function (Options $options, $value) {
                $scopes = (array) $value;

                /*
                if ($this->clientFeatureChecker->canStoreUserTokens($clientId, $context)) {
                    $scopes[] = 'offline_access';
                }
                */

                return \array_unique(\array_merge($scopes, [
                    'read:me',
                    'read:jira-user',
                ]));
            });
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        return new JiraClient($this->tokenPairFactory, $resolvedConfig);
    }
}
