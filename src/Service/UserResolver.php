<?php declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service;

use Heptacom\AdminOpenAuth\Contract\LoginInterface;
use Heptacom\AdminOpenAuth\Contract\UserEmailInterface;
use Heptacom\AdminOpenAuth\Contract\UserKeyInterface;
use Heptacom\AdminOpenAuth\Contract\UserResolverInterface;
use Heptacom\AdminOpenAuth\Contract\UserTokenInterface;
use Heptacom\AdminOpenAuth\Struct\UserStruct;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\Util\Random;
use Shopware\Core\System\User\Service\UserProvisioner;

class UserResolver implements UserResolverInterface
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
     * @var LoginInterface
     */
    private $login;

    /**
     * @var UserEmailInterface
     */
    private $userEmail;

    /**
     * @var UserKeyInterface
     */
    private $userKey;

    /**
     * @var UserTokenInterface
     */
    private $userToken;

    public function __construct(
        EntityRepositoryInterface $userRepository,
        UserProvisioner $userProvisioner,
        LoginInterface $login,
        UserEmailInterface $userEmail,
        UserKeyInterface $userKey,
        UserTokenInterface $userToken
    ) {
        $this->userRepository = $userRepository;
        $this->userProvisioner = $userProvisioner;
        $this->login = $login;
        $this->userEmail = $userEmail;
        $this->userKey = $userKey;
        $this->userToken = $userToken;
    }

    public function resolve(UserStruct $user, string $state, string $clientId, Context $context): void
    {
        $userId = $this->findUserId($user, $clientId, $context);

        if ($userId === null) {
            $password = Random::getAlphanumericString(254);
            $this->userProvisioner->provision($user->getPrimaryEmail(), $password, ['email' => $user->getPrimaryEmail()]);
            $userId = $this->findUserId($user, $clientId, $context);
        }

        $this->postUpdates($user, $userId, $state, $clientId, $context);
    }

    protected function postUpdates(
        UserStruct $user,
        string $userId,
        string $state,
        string $clientId,
        Context $context
    ): void {
        if ($tokenPair = $user->getTokenPair()) {
            if (!empty($tokenPair->getRefreshToken())) {
                $this->userToken->setRefreshToken($userId, $clientId, $tokenPair->getRefreshToken(), $context);
            }

            if (!empty($tokenPair->getAccessToken())) {
                $this->userToken->setAccessToken($userId, $clientId, $tokenPair->getAccessToken(), $context);
            }
        }

        $this->userKey->add($userId, $user->getPrimaryKey(), $clientId, $context);
        $this->userEmail->add($userId, $user->getPrimaryEmail(), $clientId, $context);

        foreach ($user->getEmails() as $email) {
            $this->userEmail->add($userId, $email, $clientId, $context);
        }

        $this->login->setCredentials($state, $userId, $context);
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
