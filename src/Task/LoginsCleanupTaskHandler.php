<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Task;

use Heptacom\AdminOpenAuth\Database\LoginCollection;
use Psr\Log\LoggerInterface;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\RangeFilter;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskCollection;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: LoginsCleanupTask::class)]
class LoginsCleanupTaskHandler extends ScheduledTaskHandler
{
    /**
     * @param EntityRepository<ScheduledTaskCollection> $scheduledTaskRepository
     * @param EntityRepository<LoginCollection> $loginsRepository
     */
    public function __construct(
        EntityRepository $scheduledTaskRepository,
        private readonly EntityRepository $loginsRepository,
        private readonly LoggerInterface $logger,
        LoggerInterface $exceptionLogger,
    ) {
        parent::__construct($scheduledTaskRepository, $exceptionLogger);
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
