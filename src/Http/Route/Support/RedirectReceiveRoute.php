<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route\Support;

use Heptacom\AdminOpenAuth\Contract\Client\ClientContract;
use Heptacom\AdminOpenAuth\Contract\Client\ClientFactoryContract;
use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\AdminOpenAuth\Contract\RoleAssignment;
use Heptacom\AdminOpenAuth\Contract\Route\Exception\RedirectReceiveException;
use Heptacom\AdminOpenAuth\Contract\Route\Exception\RedirectReceiveMissingStateException;
use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Database\ClientRuleCollection;
use Heptacom\AdminOpenAuth\Service\Rule\ClientRuleExecutor;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RedirectReceiveRoute
{
    public function __construct(
        private readonly ClientFactoryContract $clientFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ClientRuleExecutor $clientRuleExecutor,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws RedirectReceiveException
     */
    public function onReceiveRequest(
        RequestInterface $request,
        string $providerKey,
        array $configuration,
        RedirectBehaviour $behaviour,
        ClientRuleCollection $rules,
    ): User {
        \parse_str($request->getUri()->getQuery(), $getParams);

        $postParams = [];
        if ($request->getHeaderLine('content-type') === 'application/x-www-form-urlencoded') {
            \parse_str((string) $request->getBody(), $postParams);
        }

        $params = \array_merge($getParams, $postParams);

        $state = (string) ($params[$behaviour->stateKey] ?? '');
        $code = (string) ($params[$behaviour->codeKey] ?? '');

        if ($state === '' && $behaviour->expectState) {
            throw new RedirectReceiveMissingStateException($params, $behaviour->stateKey);
        }

        $client = $this->clientFactory->create($providerKey, $configuration);
        $user = $client->getUser($state, $code, $behaviour);
        $user->addArrayExtension('requestState', [
            'requestState' => $state,
        ]);

        $this->executeRules($rules, $user, $client, $configuration);

        $this->eventDispatcher->dispatch(new UserRedirectReceivedEvent($user, $request, $behaviour));

        return $user;
    }

    private function executeRules(
        ClientRuleCollection $rules,
        User $user,
        ClientContract $client,
        array $clientConfiguration
    ): void {
        $ruleScope = new OAuthRuleScope($user, $client, $clientConfiguration, Context::createDefaultContext(), $this->logger);
        $client->prepareOAuthRuleScope($ruleScope);

        $roleAssignment = new RoleAssignment();
        $user->addExtension('roleAssignment', $roleAssignment);

        $this->clientRuleExecutor->executeRules($rules, $ruleScope);
    }
}
