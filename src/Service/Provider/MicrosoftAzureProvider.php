<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Provider\MicrosoftAzureClient;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MicrosoftAzureProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'microsoft_azure';

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
            ])
            ->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('scopes', 'array');
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
        return new MicrosoftAzureClient($this->tokenPairFactory, $resolvedConfig);
    }
}
