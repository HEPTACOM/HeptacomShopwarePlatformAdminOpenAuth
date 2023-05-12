<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\AuthorizationUrl;

use Heptacom\AdminOpenAuth\Contract\AuthorizationUrl\LoginUrlGeneratorInterface;
use Heptacom\AdminOpenAuth\Contract\ClientFeatureCheckerInterface;
use Heptacom\AdminOpenAuth\Contract\ClientLoaderInterface;
use Heptacom\AdminOpenAuth\Exception\LoadClientException;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Shopware\Core\Framework\Context;

final class LoginUrlGenerator implements LoginUrlGeneratorInterface
{
    public function __construct(private readonly ClientFeatureCheckerInterface $clientFeatureChecker, private readonly ClientLoaderInterface $clientLoader)
    {
    }

    public function generate(
        string $clientId,
        string $state,
        RedirectBehaviour $redirectBehaviour,
        Context $context
    ): string {
        if (!$this->clientFeatureChecker->canLogin($clientId, $context)) {
            throw new LoadClientException('Client can not login', $clientId);
        }

        return $this->clientLoader->load($clientId, $context)->getLoginUrl($state, $redirectBehaviour);
    }
}
