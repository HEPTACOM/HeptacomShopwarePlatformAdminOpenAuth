<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Token;

use Heptacom\OpenAuth\Token\Contract\RefreshableTokenContract;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class AuthorizedHttpClient implements ClientInterface
{
    /**
     * @var ClientInterface
     */
    private $decorated;

    /**
     * @var RefreshableTokenContract
     */
    private $refreshableToken;

    public function __construct(ClientInterface $decorated, RefreshableTokenContract $refreshableToken)
    {
        $this->decorated = $decorated;
        $this->refreshableToken = $refreshableToken;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->decorated->sendRequest($this->refreshableToken->authorizeRequest($request));
    }
}
