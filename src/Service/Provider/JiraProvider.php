<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Provider\JiraClient;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Heptacom\OpenAuth\Token\Contract\TokenPairFactoryContract;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JiraProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'jira';

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
        return static::PROVIDER_NAME;
    }

    public function getConfigurationTemplate(): OptionsResolver
    {
        return parent::getConfigurationTemplate()
            ->setDefined([
                'clientId',
                'clientSecret',
                'scopes',
            ])->setRequired([
                'clientId',
                'clientSecret',
            ])->setDefaults([
                'scopes' => [],
            ])->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
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

    public function getInitialConfiguration(): array
    {
        $result = parent::getInitialConfiguration();

        $result['clientId'] = '';
        $result['clientSecret'] = '';

        return $result;
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        return new JiraClient($this->tokenPairFactory, $resolvedConfig);
    }
}
