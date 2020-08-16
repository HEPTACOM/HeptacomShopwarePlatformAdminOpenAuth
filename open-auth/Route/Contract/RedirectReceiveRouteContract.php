<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Route\Contract;

use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Client\Contract\ClientFactoryContract;
use Heptacom\OpenAuth\Route\Exception\RedirectReceiveException;
use Heptacom\OpenAuth\Route\Exception\RedirectReceiveMissingStateException;
use Heptacom\OpenAuth\Route\UserRedirectReceivedEvent;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\RequestInterface;

class RedirectReceiveRouteContract
{
    /**
     * @var ClientFactoryContract
     */
    private $clientFactory;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    public function __construct(ClientFactoryContract $clientFactory, EventDispatcherInterface $eventDispatcher)
    {
        $this->clientFactory = $clientFactory;
        $this->eventDispatcher = $eventDispatcher;
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
        \parse_str($request->getUri()->getQuery(), $params);

        $state = $params[$behaviour->getStateKey()] ?? '';
        $code = $params[$behaviour->getCodeKey()] ?? null;

        if ($state === '' && $behaviour->isExpectState()) {
            throw new RedirectReceiveMissingStateException($params, $behaviour->getStateKey());
        }

        $client = $this->clientFactory->create($providerKey, $configuration);
        $user = $client->getUser($state, $code, $behaviour)->addPassthrough('requestState', $state);

        $this->eventDispatcher->dispatch(new UserRedirectReceivedEvent($user, $request, $behaviour));

        return $user;
    }
}
