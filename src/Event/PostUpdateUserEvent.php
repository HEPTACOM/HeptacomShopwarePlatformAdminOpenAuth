<?php

namespace Heptacom\AdminOpenAuth\Event;

use Heptacom\AdminOpenAuth\Contract\User;
use Shopware\Core\Framework\Context;
use Shopware\Core\System\SalesChannel\SalesChannelEntity;
use Symfony\Contracts\EventDispatcher\Event;

class PostUpdateUserEvent extends Event
{
    public function __construct(
        public readonly User $user,
        public readonly string $userId,
        public readonly bool $isNew,
        public readonly string $clientId,
        public readonly ?SalesChannelEntity $salesChannel,
        public readonly Context $context,
    ) {
    }
}
