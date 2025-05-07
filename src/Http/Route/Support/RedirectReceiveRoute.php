<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route\Support;

use Heptacom\AdminOpenAuth\Contract\Client\ClientFactoryContract;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\AdminOpenAuth\Contract\Route\Exception\RedirectReceiveException;
use Heptacom\AdminOpenAuth\Contract\Route\Exception\RedirectReceiveMissingStateException;
use Heptacom\AdminOpenAuth\Contract\User;
use Heptacom\AdminOpenAuth\Database\ClientRuleCollection;
use Heptacom\AdminOpenAuth\Database\LoginCollection;
use Heptacom\AdminOpenAuth\Database\LoginDefinition;
use Heptacom\AdminOpenAuth\Database\LoginEntity;
use Heptacom\AdminOpenAuth\Service\ClientRuleValidator;
use Heptacom\AdminOpenAuth\Database\ClientEntity;
use Psr\Http\Message\RequestInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class RedirectReceiveRoute
{
    /**
     * @param EntityRepository<LoginCollection> $loginRepository
     */
    public function __construct(
        private readonly ClientFactoryContract $clientFactory,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ClientRuleValidator $clientRuleValidator,
        #[Autowire(service: LoginDefinition::ENTITY_NAME . '.repository')]
        private readonly EntityRepository $loginRepository,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @throws RedirectReceiveException
     */
    public function onReceiveRequest(
        RequestInterface $request,
        ClientEntity $client,
        RedirectBehaviour $behaviour,
        ClientRuleCollection $rules,
        Context $context
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

        $loginCriteria = new Criteria();
        $loginCriteria->addFilter(new EqualsFilter('state', $state));
        /** @var LoginEntity $login */
        $login = $this->loginRepository->search($loginCriteria, $context)->first();

        $oauthClient = $this->clientFactory->create($client->provider, $client->config);
        $user = $oauthClient->getUser($state, $code, $behaviour);
        $user->addArrayExtension('requestState', [
            'requestState' => $state,
            'salesChannelId' => $login->salesChannelId,
        ]);

        $client->addExtension('oauthClient', $oauthClient);

        $this->eventDispatcher->dispatch(
            new UserRedirectReceivedEvent(
                $user,
                $client,
                $request,
                $behaviour
            )
        );

        return $user;
    }
}
