<?php declare(strict_types=1);

namespace Heptacom\OpenAuth\Token;

use Heptacom\OpenAuth\Client\Contract\ClientContract;
use Heptacom\OpenAuth\Struct\TokenPairStruct;
use Heptacom\OpenAuth\Token\Contract\RefreshableTokenContract;
use Heptacom\OpenAuth\Token\Exception\NoRefreshTokenGivenException;

class RefreshableClientToken extends RefreshableTokenContract
{
    /**
     * @var ClientContract
     */
    private $client;

    /**
     * @var TokenPairStruct
     */
    private $base;

    /**
     * @var int
     */
    private $secondsOff;

    public function __construct(ClientContract $client, TokenPairStruct $base, int $secondsOff = 5)
    {
        $this->client = $client;
        $this->base = $base;
        $this->secondsOff = $secondsOff;
    }

    public function getFreshToken(bool $forceRefresh = false): TokenPairStruct
    {
        $now = time() - $this->secondsOff;
        $expiration = $this->base->getExpiresAt();

        if ($forceRefresh || !$expiration instanceof \DateTimeInterface || $expiration->getTimestamp() <= $now) {
            $refreshToken = $this->base->getRefreshToken();

            if (!\is_string($refreshToken)) {
                throw new NoRefreshTokenGivenException();
            }

            $this->base = $this->client->refreshToken($refreshToken);
        }

        return $this->base;
    }
}
