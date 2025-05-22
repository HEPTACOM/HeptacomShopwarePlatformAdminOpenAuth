<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Twig;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * This is quite similar to the \Shopware\Storefront\Framework\Twig\Extension\ConfigExtension but exists to work without shopware/storefront.
 */
final class ConfigExtension extends AbstractExtension
{
    public function __construct(private readonly SystemConfigService $systemConfigService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('heptacom_admin_auth_config', $this->config(...), ['needs_context' => false]),
        ];
    }

    public function config(?string $key = null): mixed
    {
        if ($key === null || $key === '') {
            return $this->systemConfigService->getDomain('KskHeptacomAdminOpenAuth.config');
        }

        return $this->systemConfigService->get('KskHeptacomAdminOpenAuth.config.' . $key);
    }
}
