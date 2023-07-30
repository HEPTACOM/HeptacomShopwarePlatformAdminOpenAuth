<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route\Support;

use Heptacom\AdminOpenAuth\Contract\Client\ClientFactoryContract;
use Heptacom\AdminOpenAuth\Contract\Route\Exception\RedirectReceiveException;
use Heptacom\AdminOpenAuth\Contract\Route\Exception\RedirectReceiveMissingStateException;
use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\RequestInterface;

class RedirectReceiveRoute
{
    public function __construct(
        private ClientFactoryContract $clientFactory,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }

    /**
     * @throws RedirectReceiveException
     */
    public function onReceiveRequest(
        RequestInterface $request,
        string $providerKey,
        array $configuration,
        RedirectBehaviour $behaviour
    ): UserStruct {
        \parse_str($request->getUri()->getQuery(), $getParams);

        $postParams = [];
        if ($request->getHeaderLine('content-type') === 'application/x-www-form-urlencoded') {
            \parse_str((string)$request->getBody(), $postParams);
        }

        $params = array_merge($getParams, $postParams);

        $state = (string) ($params[$behaviour->getStateKey()] ?? '');
        $code = (string) ($params[$behaviour->getCodeKey()] ?? '');

        if ($state === '' && $behaviour->isExpectState()) {
            throw new RedirectReceiveMissingStateException($params, $behaviour->getStateKey());
        }

        $client = $this->clientFactory->create($providerKey, $configuration);
        $user = $client->getUser($state, $code, $behaviour)->addPassthrough('requestState', $state);

        $this->eventDispatcher->dispatch(new UserRedirectReceivedEvent($user, $request, $behaviour));

        return $user;
    }
}
