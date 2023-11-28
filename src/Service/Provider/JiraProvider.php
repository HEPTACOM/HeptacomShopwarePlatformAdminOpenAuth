<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\OpenAuth\Atlassian;
use Heptacom\AdminOpenAuth\Component\Provider\JiraClient;
use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\ClientProvider\ClientProviderContract;
use Heptacom\AdminOpenAuth\Service\TokenPairFactoryContract;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class JiraProvider extends ClientProviderContract
{
    public const PROVIDER_NAME = 'jira';

    public function __construct(
        private readonly TokenPairFactoryContract $tokenPairFactory,
    ) {
    }

    public function provides(): string
    {
        return static::PROVIDER_NAME;
    }

    public function getConfigurationTemplate(): OptionsResolver
    {
        return parent::getConfigurationTemplate()
            ->setDefined([
                'id',
                'clientId',
                'clientSecret',
                'scopes',
                // TODO remove in v5
                'redirectUri',
            ])->setRequired([
                'clientId',
                'clientSecret',
            ])->setDefaults([
                'scopes' => [],
                'redirectUri' => null,
            ])->setAllowedTypes('clientId', 'string')
            ->setAllowedTypes('clientSecret', 'string')
            ->setAllowedTypes('scopes', 'array')
            ->setDeprecated(
                'redirectUri',
                'heptacom/shopware-platform-admin-open-auth',
                '*',
                'Use route api.heptacom.admin_open_auth.provider.redirect-url instead to live generate redirectUri'
            )
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
        return new JiraClient($this->tokenPairFactory, new Atlassian($resolvedConfig));
    }
}
