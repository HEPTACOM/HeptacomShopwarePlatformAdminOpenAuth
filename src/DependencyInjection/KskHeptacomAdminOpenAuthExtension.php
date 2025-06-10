<?php

namespace Heptacom\AdminOpenAuth\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\AbstractExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

class KskHeptacomAdminOpenAuthExtension extends AbstractExtension
{
    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->import(__DIR__ . '/../Resources/config/packages/features.php', 'php');
    }
}
