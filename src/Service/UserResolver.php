<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Contract\UserEmailInterface;
use Heptacom\AdminOpenAuth\Contract\UserKeyInterface;
use Heptacom\AdminOpenAuth\Contract\UserResolverInterface;
use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\OpenAuth\Struct\UserStructExtension;
use Heptacom\OpenAuth\Struct\UserStruct;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\System\User\Service\UserProvisioner;

class UserResolver implements UserResolverInterface
{
    private EntityRepositoryInterface $userRepository;

    private UserProvisioner $userProvisioner;

    private LoginInterface $login;

    private UserEmailInterface $userEmail;

    private UserKeyInterface $userKey;

    private UserTokenInterface $userToken;

    private ClientFeatureCheckerInterface $clientFeatureChecker;

    public function __construct(
        EntityRepositoryInterface $userRepository,
        UserProvisioner $userProvisioner,
        LoginInterface $login,
        UserEmailInterface $userEmail,
        UserKeyInterface $userKey,
        UserTokenInterface $userToken,
        ClientFeatureCheckerInterface $clientFeatureChecker
    ) {
        $this->userRepository = $userRepository;
        $this->userProvisioner = $userProvisioner;
        $this->login = $login;
        $this->userEmail = $userEmail;
        $this->userKey = $userKey;
        $this->userToken = $userToken;
        $this->clientFeatureChecker = $clientFeatureChecker;
    }

    public function resolve(UserStruct $user, string $state, string $clientId, Context $context): void
    {
        $userId = $this->login->getUser($state, $context) ?? $this->findUserId($user, $clientId, $context);
        $isNew = false;

        if ($userId === null) {
            $isNew = true;
            $password = Random::getAlphanumericString(254);
            $this->userProvisioner->provision($user->getPrimaryEmail(), $password, ['email' => $user->getPrimaryEmail()]);

            $userId = $this->findUserId($user, $clientId, $context);
        }

        $this->postUpdates($user, $userId, $state, $isNew, $clientId, $context);
    }

    protected function postUpdates(
        UserStruct $user,
        string $userId,
        string $state,
        bool $isNew,
        string $clientId,
        Context $context
    ): void {
        if ($this->clientFeatureChecker->canStoreUserTokens($clientId, $context)
            && ($tokenPair = $user->getTokenPair()) !== null) {
            if (!empty($tokenPair->getRefreshToken())) {
                $this->userToken->setToken($userId, $clientId, $tokenPair, $context);
            }
        }

        $this->userKey->add($userId, $user->getPrimaryKey(), $clientId, $context);
        $this->userEmail->add($userId, $user->getPrimaryEmail(), $clientId, $context);

        foreach ($user->getEmails() as $email) {
            $this->userEmail->add($userId, $email, $clientId, $context);
        }

        $this->login->setCredentials($state, $userId, $context);

        if ($isNew) {
            /** @var UserStructExtension|null $userExtension */
            $userExtension = $user->getPassthrough()[UserStructExtension::class] ?? null;

            if (!$userExtension) {
                return;
            }

            $userUpdate = [
                'id' => $userId,
                'admin' => $userExtension->isAdmin(),
            ];

            if (!$userExtension->isAdmin()) {
                $userUpdate['aclRoles'] = \array_map(static fn (string $roleId) => ['id' => $roleId], $userExtension->getAclRoleIds());
            }

            $this->userRepository->update([$userUpdate], $context);
        }
    }

    protected function findUserId(UserStruct $user, string $clientId, Context $context): ?string
    {
        $emails = $user->getEmails();
        $emails[] = $user->getPrimaryEmail();

        if (($result = $this->userEmail->searchUser($emails, $context))->count() > 0) {
            return $result->first()->getId();
        }

        if (($result = $this->userKey->searchUser($user->getPrimaryKey(), $clientId, $context))->count() > 0) {
            return $result->first()->getId();
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('email', $emails));

        return $this->userRepository->searchIds($criteria, $context)->firstId();
    }
}
