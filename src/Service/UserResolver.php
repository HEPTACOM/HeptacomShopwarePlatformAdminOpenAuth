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
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\MultiFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\NotFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\PrefixFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\System\Language\LanguageEntity;
use Shopware\Core\System\User\Service\UserProvisioner;

class UserResolver implements UserResolverInterface
{
    private EntityRepositoryInterface $userRepository;

    private EntityRepositoryInterface $languageRepository;

    private UserProvisioner $userProvisioner;

    private LoginInterface $login;

    private UserEmailInterface $userEmail;

    private UserKeyInterface $userKey;

    private UserTokenInterface $userToken;

    private ClientFeatureCheckerInterface $clientFeatureChecker;

    public function __construct(
        EntityRepositoryInterface $userRepository,
        EntityRepositoryInterface $languageRepository,
        UserProvisioner $userProvisioner,
        LoginInterface $login,
        UserEmailInterface $userEmail,
        UserKeyInterface $userKey,
        UserTokenInterface $userToken,
        ClientFeatureCheckerInterface $clientFeatureChecker
    ) {
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

        if ($isNew) {
            /** @var UserStructExtension|null $userExtension */
            $userExtension = $user->getPassthrough()[UserStructExtension::class] ?? null;

            if (!$userExtension) {
                return;
            }

            $userChangeSet['admin'] = $userExtension->isAdmin();

            if (!$userExtension->isAdmin()) {
                $userChangeSet['aclRoles'] = \array_map(static fn (string $roleId) => ['id' => $roleId], $userExtension->getAclRoleIds());
            }
        }

        if (count($userChangeSet) > 1) {
            $this->userRepository->update([$userChangeSet], $context);
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
        $userChangeSet = [
            'id' => $userId,
        ];

        if (!$isNew && !$this->clientFeatureChecker->canKeepUserUpdated($clientId, $context)) {
            return $userChangeSet;
        }

        $userChangeSet['firstName'] = '';
        $userChangeSet['lastName'] = $user->getDisplayName();

        if (!empty($user->getFirstName()) && !empty($user->getLastName())) {
            $userChangeSet['firstName'] = $user->getFirstName();
            $userChangeSet['lastName'] = $user->getLastName();
        }

        $userChangeSet['email'] = $user->getPrimaryEmail();
        $userChangeSet['timeZone'] = $user->getTimezone();
        $userChangeSet['localeId'] = $this->findLocaleId($user->getLocale() ?? '', $context);

        return \array_filter($userChangeSet, static fn ($value) => $value !== null);
    }
}
