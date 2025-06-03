<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Contract\RoleAssignment;
use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Contract\UserEmailInterface;
use Heptacom\AdminOpenAuth\Contract\UserKeyInterface;
use Heptacom\AdminOpenAuth\Contract\UserResolverInterface;
use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\Exception\UserMismatchException;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Acl\Role\AclUserRoleDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Maintenance\User\Service\UserProvisioner;
use Shopware\Core\System\Language\LanguageCollection;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\User\UserCollection;
use Shopware\Core\System\User\UserDefinition;

final readonly class UserResolver implements UserResolverInterface
{
    /**
     * @param EntityRepository<UserCollection> $userRepository
     * @param EntityRepository<LanguageCollection> $languageRepository
     */
    public function __construct(
        private EntityRepository $userRepository,
        private EntityRepository $languageRepository,
        private Connection $connection,
        private UserProvisioner $userProvisioner,
        private LoginInterface $login,
        private UserEmailInterface $userEmail,
        private UserKeyInterface $userKey,
        private UserTokenInterface $userToken,
        private ClientFeatureCheckerInterface $clientFeatureChecker
    ) {
    }

    public function resolve(User $user, string $state, string $clientId, Context $context): void
    {
        $userId = $this->login->getUser($state, $context);
        $mappedUserId = $this->findUserId($user, $clientId, $context);
        $isNew = false;

        if ($userId !== null && $mappedUserId !== null && $userId !== $mappedUserId) {
            throw new UserMismatchException();
        }

        if ($mappedUserId === null) {
            $isNew = true;
            $password = Random::getAlphanumericString(254);
            $this->userProvisioner->provision($user->primaryEmail, $password, ['email' => $user->primaryEmail, 'admin' => 0]);

            $mappedUserId = $this->findUserId($user, $clientId, $context);
        }

        $this->postUpdates($user, $mappedUserId, $state, $isNew, $clientId, $context);
        $user->addArrayExtension('shopwareUser', ['id' => $mappedUserId]);
    }

    protected function postUpdates(
        User $user,
        string $userId,
        string $state,
        bool $isNew,
        string $clientId,
        Context $context
    ): void {
        $tokenPair = $user->tokenPair;

        if ($this->clientFeatureChecker->canStoreUserTokens($clientId, $context) && $tokenPair !== null) {
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

        $userChangeSet = $this->getUserInfoChangeSet($user, $isNew, $clientId, $context);

        $this->updateUser($userId, $userChangeSet, $isNew);
    }

    protected function findUserId(User $user, string $clientId, Context $context): ?string
    {
        $emails = $user->emails;
        $emails[] = $user->primaryEmail;
        $userEmails = $this->userEmail->searchUser($emails, $context);

        if ($userEmails->count() > 0) {
            return $userEmails->first()->getId();
        }

        $userKeys = $this->userKey->searchUser($user->primaryKey, $clientId, $context);

        if ($userKeys->count() > 0) {
            return $userKeys->first()->getId();
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('email', $emails));

        return $this->userRepository->searchIds($criteria, $context)->firstId();
    }

    protected function findLocaleId(string $localeCode, Context $context): ?string
    {
        if (empty(\trim($localeCode))) {
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

    protected function getUserInfoChangeSet(User $user, bool $isNew, string $clientId, Context $context): array
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

        $roleAssignment = $user->getExtensionOfType('roleAssignment', RoleAssignment::class);
        if ($roleAssignment instanceof RoleAssignment) {
            $userChangeSet['admin'] = $roleAssignment->isAdministrator;

            if (!$roleAssignment->isAdministrator) {
                $userChangeSet['aclRoles'] = $roleAssignment->roleIds;
            }
        } else {
            $userChangeSet['admin'] = false;
            $userChangeSet['aclRoles'] = [];
        }

        return \array_filter($userChangeSet, static fn ($value) => $value !== null);
    }

    protected function updateUser(string $userId, array $userChangeSet, bool $isNew): void
    {
        if (\count($userChangeSet) < 1) {
            return;
        }

        $aclRoles = $userChangeSet['aclRoles'] ?? null;
        unset($userChangeSet['aclRoles']);

        foreach ($userChangeSet as $key => $newValue) {
            if (\str_ends_with($key, '_id')) {
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
            ->select(...\array_keys($userChangeSet))
            ->from(UserDefinition::ENTITY_NAME)
            ->where('id = :id')
            ->setParameter('id', Uuid::fromHexToBytes($userId))
            ->executeQuery()
            ->fetchAssociative();

        if (!$user) {
            return true;
        }

        foreach ($userChangeSet as $key => $newValue) {
            if (\str_ends_with($key, '_id')) {
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
            ->fetchAllAssociative();

        if ($currentAclRoleIds === false) {
            $currentAclRoleIds = [];
        }

        $currentAclRoleIds = Uuid::fromBytesToHexList(\array_column($currentAclRoleIds, 'acl_role_id'));

        // delete old
        $toDelete = \array_diff($currentAclRoleIds, $newAclRoles);
        if (\count($toDelete) > 0) {
            $binToDelete = Uuid::fromHexToBytesList($toDelete);
            $this->connection->createQueryBuilder()
                ->delete(AclUserRoleDefinition::ENTITY_NAME)
                ->where('user_id = :userId')
                ->andWhere('acl_role_id IN (:aclRoleIds)')
                ->setParameter('userId', $binUserId)
                ->setParameter('aclRoleIds', $binToDelete, ArrayParameterType::STRING)
                ->execute();
        }

        // insert new
        $toAdd = \array_diff($newAclRoles, $currentAclRoleIds);
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
