<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Maintenance\User\Service\UserProvisioner;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Contract\UserEmailInterface;
use Heptacom\AdminOpenAuth\Contract\UserKeyInterface;
use Heptacom\AdminOpenAuth\Contract\UserResolverInterface;
use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\OpenAuth\Struct\UserStructExtension;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Acl\Role\AclUserRoleDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\User\UserDefinition;

final class UserResolver implements UserResolverInterface
{
    public function __construct(
        private readonly EntityRepository $userRepository,
        private readonly EntityRepository $languageRepository,
        private readonly Connection $connection,
        private readonly UserProvisioner $userProvisioner,
        private readonly LoginInterface $login,
        private readonly UserEmailInterface $userEmail,
        private readonly UserKeyInterface $userKey,
        private readonly UserTokenInterface $userToken,
        private readonly ClientFeatureCheckerInterface $clientFeatureChecker
    ) {
    }

    public function resolve(User $user, string $state, string $clientId, Context $context): void
    {
        $userId = $this->login->getUser($state, $context) ?? $this->findUserId($user, $clientId, $context);
        $isNew = false;

        if ($userId === null) {
            $isNew = true;
            $password = Random::getAlphanumericString(254);
            $this->userProvisioner->provision($user->primaryEmail, $password, ['email' => $user->primaryEmail]);

            $userId = $this->findUserId($user, $clientId, $context);
        }

        $this->postUpdates($user, $userId, $state, $isNew, $clientId, $context);
    }

    protected function postUpdates(
        User $user,
        string $userId,
        string $state,
        bool $isNew,
        string $clientId,
        Context $context
    ): void {
        if ($this->clientFeatureChecker->canStoreUserTokens($clientId, $context)
            && ($tokenPair = $user->tokenPair) !== null) {
            if (!empty($tokenPair->refreshToken)) {
                $this->userToken->setToken($userId, $clientId, $tokenPair, $context);
            }
        }

        $this->userKey->add($userId, $user->primaryKey, $clientId, $context);
        $this->userEmail->add($userId, $user->primaryEmail, $clientId, $context);

        foreach ($user->emails as $email) {
            $this->userEmail->add($userId, $email, $clientId, $context);
        }

        $this->login->setCredentials($state, $userId, $context);

        $userChangeSet = $this->getUserInfoChangeSet($userId, $user, $isNew, $clientId, $context);
        $aclRoles = null;

        if ($isNew) {
            /** @var UserStructExtension|null $userExtension */
            $userExtension = $user->getExtensionOfType(UserStructExtension::class, UserStructExtension::class);

            if (!$userExtension) {
                return;
            }

            $userChangeSet['admin'] = $userExtension->isAdmin();

            if (!$userExtension->isAdmin()) {
                $aclRoles = $userExtension->getAclRoleIds();
            }
        }

        $this->updateUser($userId, $userChangeSet, $aclRoles, $isNew);
    }

    protected function findUserId(User $user, string $clientId, Context $context): ?string
    {
        $emails = $user->emails;
        $emails[] = $user->primaryEmail;

        if (($result = $this->userEmail->searchUser($emails, $context))->count() > 0) {
            return $result->first()->getId();
        }

        if (($result = $this->userKey->searchUser($user->primaryKey, $clientId, $context))->count() > 0) {
            return $result->first()->getId();
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('email', $emails));

        return $this->userRepository->searchIds($criteria, $context)->firstId();
    }

    protected function findLocaleId(string $localeCode, Context $context): ?string
    {
        if (empty(trim($localeCode))) {
            return null;
        }

        $filters = [
            new EqualsFilter('locale.code', $localeCode),
            new PrefixFilter('locale.code', $localeCode),
        ];

        foreach ($filters as $filter) {
            $criteria = new Criteria();
            $criteria->addFilter($filter);
            $criteria->setLimit(1);

            $language = $this->languageRepository->search($criteria, $context)->first();

            if ($language instanceof LanguageEntity) {
                return $language->getLocaleId();
            }
        }

        return null;
    }

    protected function getUserInfoChangeSet(string $userId, User $user, bool $isNew, string $clientId, Context $context): array
    {
        $userChangeSet = [];

        if (!$isNew && !$this->clientFeatureChecker->canKeepUserUpdated($clientId, $context)) {
            return $userChangeSet;
        }

        $userChangeSet['first_name'] = '';
        $userChangeSet['last_name'] = $user->displayName;

        if (!empty($user->firstName) && !empty($user->lastName)) {
            $userChangeSet['first_name'] = $user->firstName;
            $userChangeSet['last_name'] = $user->lastName;
        }

        $userChangeSet['email'] = $user->primaryEmail;
        $userChangeSet['time_zone'] = $user->timezone;
        $userChangeSet['locale_id'] = $this->findLocaleId($user->locale ?? '', $context);

        return \array_filter($userChangeSet, static fn ($value) => $value !== null);
    }

    protected function updateUser(string $userId, array $userChangeSet, ?array $aclRoles, bool $isNew): void
    {
        if (\count($userChangeSet) < 1) {
            return;
        }

        foreach ($userChangeSet as $key => $newValue) {
            if (str_ends_with($key, '_id')) {
                $userChangeSet[$key] = Uuid::fromHexToBytes($newValue);
            }
        }

        // check with database if update is required
        if ($isNew || $this->isUserChanged($userId, $userChangeSet)) {
            $userChangeSet['updated_at'] = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);
            $this->connection->update(UserDefinition::ENTITY_NAME, $userChangeSet, ['id' => Uuid::fromHexToBytes($userId)], ['admin' => Types::BOOLEAN]);
        }

        // check if acl roles are changed
        if ($aclRoles !== null) {
            $this->updateAclRoles($userId, $aclRoles);
        }
    }

    protected function isUserChanged(string $userId, array $userChangeSet): bool
    {
        /** @var array<array-key, mixed>|false $user */
        $user = $this->connection->createQueryBuilder()
            ->select(array_keys($userChangeSet))
            ->from(UserDefinition::ENTITY_NAME)
            ->where('id = :id')
            ->setParameter('id', Uuid::fromHexToBytes($userId))
            ->execute()
            ->fetchAssociative();

        if (!$user) {
            return true;
        }

        foreach ($userChangeSet as $key => $newValue) {
            if (str_ends_with($key, '_id')) {
                $newValue = Uuid::fromBytesToHex($newValue);
            }

            if ($user[$key] !== $newValue) {
                return true;
            }
        }

        return false;
    }

    protected function updateAclRoles(string $userId, array $newAclRoles): void
    {
        $binUserId = Uuid::fromHexToBytes($userId);

        $currentAclRoleIds = $this->connection->createQueryBuilder()
            ->select('acl_role_id')
            ->from(AclUserRoleDefinition::ENTITY_NAME)
            ->where('user_id = :userId')
            ->setParameter('userId', $binUserId)
            ->execute()
            ->fetchAssociative();

        if ($currentAclRoleIds === false) {
            $currentAclRoleIds = [];
        }

        $currentAclRoleIds = Uuid::fromBytesToHexList(array_column($currentAclRoleIds, 'acl_role_id'));

        // delete old
        $toDelete = array_diff($currentAclRoleIds, $newAclRoles);
        if (\count($toDelete) > 0) {
            $this->connection->createQueryBuilder()
                ->delete(AclUserRoleDefinition::ENTITY_NAME)
                ->where('user_id = :userId')
                ->andWhere('acl_role_id IN (:aclRoleIds)')
                ->setParameter('userId', $binUserId)
                ->setParameter('aclRoleIds', $toDelete, Connection::PARAM_STR_ARRAY)
                ->execute();
        }

        // insert new
        $toAdd = array_diff($newAclRoles, $currentAclRoleIds);
        if (\count($toAdd) > 0) {
            foreach ($toAdd as $aclRoleId) {
                $this->connection->insert(
                    AclUserRoleDefinition::ENTITY_NAME,
                    [
                        'user_id' => $binUserId,
                        'acl_role_id' => Uuid::fromHexToBytes($aclRoleId),
                        'created_at' => (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT),
                    ]
                );
            }
        }
    }
}
