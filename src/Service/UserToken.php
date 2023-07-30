<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\TokenPair;
use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\Database\UserTokenCollection;
use Heptacom\AdminOpenAuth\Database\UserTokenEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

final class UserToken implements UserTokenInterface
{
    public function __construct(private readonly EntityRepository $userTokensRepository)
    {
    }

    public function setToken(string $userId, string $clientId, TokenPair $token, Context $context): string
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
                'accessToken' => $token->accessToken,
                'expiresAt' => $token->expiresAt,
            ];

            if ($token->refreshToken !== null) {
                $payload['refreshToken'] = $token->refreshToken;
            }

            $this->userTokensRepository->update([$payload], $context);

            return $id;
        }

        $id = Uuid::randomHex();
        $this->userTokensRepository->create([[
            'id' => $id,
            'userId' => $userId,
            'refreshToken' => $token->refreshToken,
            'accessToken' => $token->accessToken,
            'expiresAt' => $token->expiresAt,
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
