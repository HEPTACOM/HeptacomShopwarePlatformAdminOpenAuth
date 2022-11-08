<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Doctrine\DBAL\Connection;
use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Contract\UserEmailInterface;
use Heptacom\AdminOpenAuth\Contract\UserKeyInterface;
use Heptacom\AdminOpenAuth\Contract\UserResolverInterface;
use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\OpenAuth\Struct\UserStructExtension;
use Heptacom\OpenAuth\Struct\UserStruct;
use Shopware\Core\Defaults;
use Shopware\Core\Framework\Api\Acl\Role\AclUserRoleDefinition;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\User\Service\UserProvisioner;
use Shopware\Core\System\User\UserDefinition;

class UserResolver implements UserResolverInterface
{
    private EntityRepositoryInterface $userRepository;

    private EntityRepositoryInterface $languageRepository;

    private Connection $connection;

    private UserProvisioner $userProvisioner;

    private LoginInterface $login;

    private UserEmailInterface $userEmail;

    private UserKeyInterface $userKey;

    private UserTokenInterface $userToken;

    private ClientFeatureCheckerInterface $clientFeatureChecker;

    public function __construct(
        EntityRepositoryInterface $userRepository,
        EntityRepositoryInterface $languageRepository,
        Connection $connection,
        UserProvisioner $userProvisioner,
        LoginInterface $login,
        UserEmailInterface $userEmail,
        UserKeyInterface $userKey,
        UserTokenInterface $userToken,
        ClientFeatureCheckerInterface $clientFeatureChecker
    ) {
        $this->connection = $connection;
        $this->userRepository = $userRepository;
        $this->languageRepository = $languageRepository;
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

        $userChangeSet = $this->getUserInfoChangeSet($userId, $user, $isNew, $clientId, $context);
        $aclRoles = null;

        if ($isNew) {
            /** @var UserStructExtension|null $userExtension */
            $userExtension = $user->getPassthrough()[UserStructExtension::class] ?? null;

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

    protected function getUserInfoChangeSet(string $userId, UserStruct $user, bool $isNew, string $clientId, Context $context): array
    {
        $userChangeSet = [];

        if (!$isNew && !$this->clientFeatureChecker->canKeepUserUpdated($clientId, $context)) {
            return $userChangeSet;
        }

        $userChangeSet['first_name'] = '';
        $userChangeSet['last_name'] = $user->getDisplayName();

        if (!empty($user->getFirstName()) && !empty($user->getLastName())) {
            $userChangeSet['first_name'] = $user->getFirstName();
            $userChangeSet['last_name'] = $user->getLastName();
        }

        $userChangeSet['email'] = $user->getPrimaryEmail();
        $userChangeSet['time_zone'] = $user->getTimezone();
        $userChangeSet['locale_id'] = $this->findLocaleId($user->getLocale() ?? '', $context);

        return \array_filter($userChangeSet, static fn ($value) => $value !== null);
    }

    protected function updateUser(string $userId, array $userChangeSet, ?array $aclRoles, bool $isNew): void
    {
        if (\count($userChangeSet) < 1) {
            return;
        }

        foreach ($userChangeSet as $key => $newValue) {
            if (substr($key, -3) === '_id') {
                $userChangeSet[$key] = Uuid::fromHexToBytes($newValue);
            }
        }

        // check with database if update is required
        if ($isNew || $this->isUserChanged($userId, $userChangeSet)) {
            $userChangeSet['updated_at'] = (new \DateTime())->format(Defaults::STORAGE_DATE_TIME_FORMAT);
            $this->connection->update(UserDefinition::ENTITY_NAME, $userChangeSet, ['id' => Uuid::fromHexToBytes($userId)]);
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
            if (substr($key, -3) === '_id') {
                $newValue = Uuid::fromHexToBytes($newValue);
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
