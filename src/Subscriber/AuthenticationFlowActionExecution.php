<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Subscriber;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RoleAssignment;
use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Database\ClientRuleCollection;
use Heptacom\AdminOpenAuth\Http\Route\Support\UserRedirectAuthenticationEvent;
use Heptacom\AdminOpenAuth\Http\Route\Support\UserRedirectReceivedEvent;
use Heptacom\AdminOpenAuth\Service\Rule\ClientRuleExecutor;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

class AuthenticationFlowActionExecution
{
    public function __construct(
        private readonly ClientRuleExecutor $clientRuleExecutor,
        private readonly LoggerInterface $logger,
    ) {
    }

    #[AsEventListener(UserRedirectReceivedEvent::class)]
    public function preAuthentication(UserRedirectReceivedEvent $event): void
    {
        $this->executeRules(
            $event->client->rules,
            $event->user,
            $event->client->getExtension('oauthClient'),
            $event->client->config
        );
    }

    #[AsEventListener(UserRedirectAuthenticationEvent::class)]
    public function postAuthentication(UserRedirectAuthenticationEvent $event): void
    {
        $this->executeRules(
            $event->client->rules,
            $event->user,
            $event->client->getExtension('oauthClient'),
            $event->client->config
        );
    }

    private function executeRules(
        ClientRuleCollection $rules,
        User $user,
        ClientContract $client,
        array $clientConfiguration
    ): void {
        $ruleScope = new OAuthRuleScope(
            $user,
            $client,
            $clientConfiguration,
            Context::createDefaultContext(),
            $this->logger
        );
        $client->prepareOAuthRuleScope($ruleScope);

        if (!$user->hasExtensionOfType('roleAssignment', RoleAssignment::class)) {
            $roleAssignment = new RoleAssignment();
            $user->addExtension('roleAssignment', $roleAssignment);
        }

        $this->clientRuleExecutor->executeRules($rules, $ruleScope);
    }
}
