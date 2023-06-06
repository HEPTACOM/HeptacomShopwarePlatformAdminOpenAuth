<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Task;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class LoginsCleanupTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'heptacom.admin-open-auth.logins.cleanup';
    }

    public static function getDefaultInterval(): int
    {
        return 300;
    }
}
