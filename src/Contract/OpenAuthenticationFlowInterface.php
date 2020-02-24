<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Shopware\Core\Framework\Context;

interface OpenAuthenticationFlowInterface
{
    /**
     * @throws LoadClientException
     */
    public function getRedirectUrl(string $clientId, Context $context): string;

    /**
     * @throws LoadClientException
     */
    public function upsertUser(string $clientId, string $state, string $code, Context $context): void;

    public function popCredentials(string $state, Context $context): ?array;

    public function getLoginRoutes(Context $context): array;
}
