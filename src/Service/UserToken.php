<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\Database\UserTokenCollection;
use Heptacom\AdminOpenAuth\Database\UserTokenEntity;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class UserToken implements UserTokenInterface
{
    private EntityRepositoryInterface $userTokensRepository;

    public function __construct(EntityRepositoryInterface $userTokensRepository)
    {
        $this->userTokensRepository = $userTokensRepository;
    }

    public function setToken(string $userId, string $clientId, TokenPairStruct $token, Context $context): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(
            new EqualsFilter('userId', $userId),
            new EqualsFilter('clientId', $clientId)
        );
        $exists = $this->userTokensRepository->searchIds($criteria, $context);

        if ($exists->getTotal() > 0) {
            $id = $exists->firstId();
            $payload = [
                'id' => $id,
                'accessToken' => $token->getAccessToken(),
                'expiresAt' => $token->getExpiresAt(),
            ];

            if ($token->getRefreshToken() !== null) {
                $payload['refreshToken'] = $token->getRefreshToken();
            }

            $this->userTokensRepository->update([$payload], $context);

            return $id;
        }

        $id = Uuid::randomHex();
        $this->userTokensRepository->create([[
            'id' => $id,
            'userId' => $userId,
            'refreshToken' => $token->getRefreshToken(),
            'accessToken' => $token->getAccessToken(),
            'expiresAt' => $token->getExpiresAt(),
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
