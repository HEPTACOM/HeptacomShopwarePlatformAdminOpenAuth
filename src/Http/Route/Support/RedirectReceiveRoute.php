<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Http\Route\Support;

use Heptacom\AdminOpenAuth\Contract\Client\ClientFactoryContract;
use Heptacom\AdminOpenAuth\Contract\RedirectBehaviour;
use Heptacom\AdminOpenAuth\Contract\Route\Exception\RedirectReceiveException;
use Heptacom\AdminOpenAuth\Contract\Route\Exception\RedirectReceiveMissingStateException;
use Heptacom\AdminOpenAuth\Contract\User;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\RequestInterface;

class RedirectReceiveRoute
{
    public function __construct(
        private readonly ClientFactoryContract $clientFactory,
        private readonly EventDispatcherInterface $eventDispatcher
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

        $this->eventDispatcher->dispatch(new UserRedirectReceivedEvent($user, $request, $behaviour));

        return $user;
    }
}
