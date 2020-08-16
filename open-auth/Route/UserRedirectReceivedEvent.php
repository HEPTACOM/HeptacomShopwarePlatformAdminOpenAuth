<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Route;

use Heptacom\OpenAuth\Behaviour\RedirectBehaviour;
use Heptacom\OpenAuth\Struct\UserStruct;
use Psr\Http\Message\RequestInterface;

class UserRedirectReceivedEvent
{
    /**
     * @var UserStruct
     */
    private $user;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var RedirectBehaviour
     */
    private $behaviour;

    public function __construct(UserStruct $user, RequestInterface $request, RedirectBehaviour $behaviour)
    {
        $this->user = $user;
        $this->request = $request;
        $this->behaviour = $behaviour;
    }

    public function getUser(): UserStruct
    {
        return $this->user;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getBehaviour(): RedirectBehaviour
    {
        return $this->behaviour;
    }
}
