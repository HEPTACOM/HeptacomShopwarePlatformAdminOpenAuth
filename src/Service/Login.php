<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Database\LoginCollection;
use Heptacom\AdminOpenAuth\Database\LoginEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class Login implements LoginInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private $loginsRepository;

    public function __construct(EntityRepositoryInterface $loginsRepository)
    {
        $this->loginsRepository = $loginsRepository;
    }

    public function initiate(string $clientId, string $state, Context $context): string
    {
        $id = Uuid::randomHex();
        $this->loginsRepository->create([[
            'id' => $id,
            'clientId' => $clientId,
            'state' => $state,
        ]], $context);

        return $id;
    }

    public function setCredentials(string $state, string $userId, string $password, Context $context): bool
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('state', $state));
        $ids = $this->loginsRepository->searchIds($criteria, $context);
        $update = [];

        foreach ($ids->getIds() as $id) {
            $update[] = [
                'id' => $id,
                'password' => $password,
                'userId' => $userId,
            ];
        }

        $this->loginsRepository->update($update, $context);

        return $ids->getTotal() > 0;
    }

    public function pop(string $state, Context $context): ?LoginEntity
    {
        $criteria = new Criteria();
        $criteria->addAssociation('user');
        $criteria->addFilter(new EqualsFilter('state', $state));
        /** @var LoginCollection $logins */
        $logins = $this->loginsRepository->search($criteria, $context)->getEntities();

        if ($logins->count() > 0) {
            $deletePayload = $logins->map(function (LoginEntity $login): array {
                return ['id' => $login->getId()];
            });
            $this->loginsRepository->delete(array_values($deletePayload), $context);
        }

        return $logins->first();
    }
}
