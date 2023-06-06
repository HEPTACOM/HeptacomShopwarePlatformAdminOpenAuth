<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Task;

use Psr\Log\LoggerInterface;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;

class LoginsCleanupTaskHandler extends ScheduledTaskHandler
{
    public function __construct(
        EntityRepositoryInterface $scheduledTaskRepository,
        private EntityRepositoryInterface $loginsRepository,
        private LoggerInterface $logger,
    ) {
        parent::__construct($scheduledTaskRepository);
    }

    public static function getHandledMessages(): iterable
    {
        return [LoginsCleanupTask::class];
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();

        $criteria = new Criteria();
        $criteria->addFilter(new RangeFilter('expiresAt', [
            RangeFilter::LT => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
        ]));

        $ids = $this->loginsRepository->searchIds($criteria, $context);

        if ($ids->getTotal() > 0) {
            $this->logger->info(\sprintf('Found %d expired SSO states. Deleting expired.', $ids->getTotal()));
            $this->loginsRepository->delete(\array_map(static fn ($id) => ['id' => $id], $ids->getIds()), $context);
        }
    }
}
