<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Symfony\Component\OptionsResolver\OptionsResolver;

interface ProviderConfigurationResolverFactoryInterface
{
    public function getOptionResolver(): OptionsResolver;
}
