<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Heptacom\OpenAuth\Struct\UserStruct;
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
    public function getRedirectUrlToConnect(string $clientId, string $userId, Context $context): string;

    /**
     * @throws LoadClientException
     */
    public function upsertUser(UserStruct $user, string $clientId, string $state, Context $context): void;

    public function getLoginRoutes(Context $context): array;
}
