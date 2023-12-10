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
use Heptacom\AdminOpenAuth\Service\ClientRuleValidator;
use Psr\Http\Message\RequestInterface;
use Shopware\Core\Framework\Context;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RedirectReceiveRoute
{
    public function __construct(
        private readonly ClientFactoryContract $clientFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ClientRuleValidator $clientRuleValidator
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

        $params = array_merge($getParams, $postParams);

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

        $this->discoverRoleAssignment($rules, $user, $client, $configuration);

        $this->eventDispatcher->dispatch(new UserRedirectReceivedEvent($user, $request, $behaviour));

        return $user;
    }

    private function discoverRoleAssignment(ClientRuleCollection $rules, User $user, ClientContract $client, array $configuration): void
    {
        $roleAssignmentType = $configuration['roleAssignment'] ?? 'static';

        if ($roleAssignmentType !== 'dynamic') {
            return;
        }

        $ruleScope = new OAuthRuleScope($user, $client, $configuration, Context::createDefaultContext());
        $client->prepareOAuthRuleScope($ruleScope);

        foreach ($rules->getElements() as $rule) {
            if ($this->clientRuleValidator->isValid($rule->getId(), $ruleScope)) {
                $roleAssignment = new RoleAssignment();
                $user->addExtension('roleAssignment', $roleAssignment);

                $roleAssignment->isAdministrator = $rule->isUserBecomeAdmin();
                $roleAssignment->rules = $rule->getAclRoles()->getIds();

                break;
            }
        }
    }
}
