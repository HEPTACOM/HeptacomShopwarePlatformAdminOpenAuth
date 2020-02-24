<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth;

use Heptacom\AdminOpenAuth\Contract\UserStruct;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\System\User\Service\UserProvisioner;

class UserResolver
{
    /**
     * @var EntityRepositoryInterface
     */
    private $userRepository;

    /**
     * @var UserProvisioner
     */
    private $userProvisioner;

    /**
     * @var Login
     */
    private $login;

    /**
     * @var UserEmail
     */
    private $userEmail;

    /**
     * @var UserKey
     */
    private $userKey;

    public function __construct(
        EntityRepositoryInterface $userRepository,
        UserProvisioner $userProvisioner,
        Login $login,
        UserEmail $userEmail,
        UserKey $userKey
    ) {
        $this->userRepository = $userRepository;
        $this->userProvisioner = $userProvisioner;
        $this->login = $login;
        $this->userEmail = $userEmail;
        $this->userKey = $userKey;
    }

    public function resolve(UserStruct $user, string $state, string $clientId, Context $context): void
    {
        $userId = $this->findUserId($user, $clientId, $context);
        $password = Random::getAlphanumericString(254);

        if ($userId !== null) {
            $this->updatePassword($userId, $password, $context);
            $this->postUpdates($user, $userId, $password, $state, $clientId, $context);

            return;
        }

        $this->userProvisioner->provision($user->getPrimaryEmail(), $password, ['email' => $user->getPrimaryEmail()]);
        $userId = $this->findUserId($user, $clientId, $context);
        $this->postUpdates($user, $userId, $password, $state, $clientId, $context);
    }

    protected function postUpdates(
        UserStruct $user,
        string $userId,
        string $password,
        string $state,
        string $clientId,
        Context $context
    ): void {
        $this->userKey->add($userId, $user->getPrimaryKey(), $clientId, $context);
        $this->userEmail->add($userId, $user->getPrimaryEmail(), $clientId, $context);

        foreach ($user->getEmails() as $email) {
            $this->userEmail->add($userId, $email, $clientId, $context);
        }

        $this->login->setCredentials($state, $userId, $password, $context);
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

    private function updatePassword(string $userId, string $password, Context $context): void
    {
        $this->userRepository->update([[
            'id' => $userId,
            'password' => password_hash($password, PASSWORD_BCRYPT),
        ]], $context);
    }
}
