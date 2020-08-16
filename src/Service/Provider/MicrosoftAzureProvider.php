<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Provider\MicrosoftAzureClient;
use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Client\Contract\TokenPairFactoryContract;
use Heptacom\OpenAuth\ClientProvider\Contract\ClientProviderContract;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MicrosoftAzureProvider extends ClientProviderContract
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
        return 'microsoft_azure';
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
            ])
            ->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('redirectUri', 'string')
            ->setAllowedTypes('scopes', 'array');
    }

    public function provideClient(array $resolvedConfig): ClientContract
    {
        return new MicrosoftAzureClient($this->tokenPairFactory, $resolvedConfig);
    }
}
