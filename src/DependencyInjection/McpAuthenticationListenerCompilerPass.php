<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class McpAuthenticationListenerCompilerPass implements CompilerPassInterface
{
    private const MCP_LISTENER = 'Shopware\Core\Framework\Mcp\Authentication\McpAuthenticationListener';

    private const INNER_CLIENT_REPOSITORY = 'Heptacom\AdminOpenAuth\Service\OpenAuth\OneTimeTokenClientRepository.inner';

    #[\Override]
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition(self::MCP_LISTENER)) {
            return;
        }

        $container->getDefinition(self::MCP_LISTENER)
            ->setArgument('$clientRepository', new Reference(self::INNER_CLIENT_REPOSITORY));
    }
}
