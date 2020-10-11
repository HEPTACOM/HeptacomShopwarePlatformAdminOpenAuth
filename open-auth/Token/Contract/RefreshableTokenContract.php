<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Token\Contract;

use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Token\Exception\UnrefreshableException;
use Psr\Http\Message\RequestInterface;

abstract class RefreshableTokenContract
{
    /**
     * @throws UnrefreshableException
     */
    abstract public function getFreshToken(bool $forceRefresh = false): TokenPairStruct;

    /**
     * @throws UnrefreshableException
     */
    abstract public function authorizeRequest(RequestInterface $request): RequestInterface;
}
