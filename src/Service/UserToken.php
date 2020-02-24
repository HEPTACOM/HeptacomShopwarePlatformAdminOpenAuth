<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\Database\UserTokenCollection;
use Heptacom\AdminOpenAuth\Database\UserTokenEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class UserToken implements UserTokenInterface
{
    /**
     * @var EntityRepositoryInterface
     */
    private $userTokensRepository;

    public function __construct(EntityRepositoryInterface $userTokensRepository)
    {
        $this->userTokensRepository = $userTokensRepository;
    }

    public function setRefreshToken(string $userId, string $clientId, string $refreshToken, Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('userId', $userId),
            new EqualsFilter('clientId', $clientId)
        );
        $exists = $this->userTokensRepository->searchIds($criteria, $context);

        if ($exists->getTotal() > 0) {
            $id = $exists->firstId();
            $this->userTokensRepository->update([[
                'id' => $id,
                'refreshToken' => $refreshToken,
            ]], $context);

            return $id;
        }

        $id = Uuid::randomHex();
        $this->userTokensRepository->create([[
            'id' => $id,
            'userId' => $userId,
            'refreshToken' => $refreshToken,
            'clientId' => $clientId,
        ]], $context);

        return $id;
    }

    public function setAccessToken(string $userId, string $clientId, string $accessToken, Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('userId', $userId),
            new EqualsFilter('clientId', $clientId)
        );
        $exists = $this->userTokensRepository->searchIds($criteria, $context);

        if ($exists->getTotal() > 0) {
            $id = $exists->firstId();
            $this->userTokensRepository->update([[
                'id' => $id,
                'accessToken' => $accessToken,
            ]], $context);

            return $id;
        }

        $id = Uuid::randomHex();
        $this->userTokensRepository->create([[
            'id' => $id,
            'userId' => $userId,
            'accessToken' => $accessToken,
            'clientId' => $clientId,
        ]], $context);

        return $id;
    }

    public function getToken(string $clientId, string $userId, Context $context): ?UserTokenEntity
    {
        $criteria = new Criteria();
        $criteria->addAssociation('user');
        $criteria->addAssociation('client');
        $criteria->addFilter(
            new EqualsFilter('userId', $userId),
            new EqualsFilter('clientId', $clientId)
        );
        /** @var UserTokenCollection $userTokens */
        $userTokens = $this->userTokensRepository->search($criteria, $context)->getEntities();

        return $userTokens->first();
    }
}
