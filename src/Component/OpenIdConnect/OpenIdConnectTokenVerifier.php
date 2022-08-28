<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\JWKSet;
use Jose\Component\Signature\Algorithm\ES256;
use Jose\Component\Signature\Algorithm\ES384;
use Jose\Component\Signature\Algorithm\ES512;
use Jose\Component\Signature\Algorithm\PS256;
use Jose\Component\Signature\Algorithm\PS384;
use Jose\Component\Signature\Algorithm\PS512;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\Algorithm\RS512;
use Jose\Component\Signature\JWS;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;
use Jose\Component\Signature\Signature;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class OpenIdConnectTokenVerifier
{
    private const CACHE_TTL = 900;

    private ClientInterface $oidcHttpClient;

    private AdapterInterface $cache;

    private LoggerInterface $logger;

    private JWSSerializerManager $serializerManager;

    private JWSVerifier $verifier;

    public function __construct(
        ClientInterface $oidcHttpClient,
        AdapterInterface $cache,
        LoggerInterface $logger
    ) {
        $this->oidcHttpClient = $oidcHttpClient;
        $this->cache = $cache;
        $this->logger = $logger;

        $this->serializerManager = new JWSSerializerManager([
            new CompactSerializer(),
        ]);
        $algorithmManager = new AlgorithmManager([
            new RS256(),
            new RS512(),
            new PS256(),
            new PS384(),
            new PS512(),
            new ES256(),
            new ES384(),
            new ES512(),
        ]);
        $this->verifier = new JWSVerifier($algorithmManager);
    }

    /**
     * @throws OpenIdConnectException
     */
    public function verify(OpenIdConnectConfiguration $config, string $idToken): bool
    {
        try {
            $token = $this->serializerManager->unserialize($idToken);
            $payload = @json_decode($token->getPayload(), true) ?? [];
        } catch (\InvalidArgumentException $e) {
            throw new OpenIdConnectException('Unable to decode id_token: ' . $e->getMessage(), $e);
        }

        return $this->verifySignature($config, $token)
            && $this->verifyIssuer($config, $payload)
            && $this->verifyAudience($config, $payload)
            && $this->verifyValidityTime($payload);
    }

    protected function verifySignature(OpenIdConnectConfiguration $config, JWS $token): bool
    {
        // try to load jks
        $keys = $this->loadKeys($config);

        if (!$keys) {
            // assume the key is valid, as we have no way to check it
            return true;
        }

        // load JWT access token
        try {
            if ($token->countSignatures() === 0) {
                $message = 'Deserialized JWT token does not contain any signatures. This should not be able to happen!';
                $this->logger->critical($message);

                throw new \RuntimeException($message);
            }
        } catch (\InvalidArgumentException $e) {
            throw new OpenIdConnectException('Unable to decode id_token: ' . $e->getMessage(), $e);
        }

        // try to verify all signatures
        /**
         * @var int       $signatureIndex
         * @var Signature $signature
         */
        foreach ($token->getSignatures() as $signatureIndex => $signature) {
            $algorithm = array_merge($signature->getProtectedHeader(), $signature->getHeader())['alg'] ?? null;

            if (!$algorithm) {
                continue;
            }

            if (!$this->verifier->getSignatureAlgorithmManager()->has($algorithm)) {
                $this->logger->notice(sprintf('Could not verify JWT signature. Algorithm %s is not supported.', $algorithm));

                continue;
            }

            if (!$this->verifier->verifyWithKeySet($token, $keys, $signatureIndex)) {
                return false;
            }
        }

        $this->logger->debug('JWT signature successfully verified.');

        return true;
    }

    protected function verifyIssuer(OpenIdConnectConfiguration $config, array $payload): bool
    {
        $issuer = $payload['iss'] ?? null;

        return !$config->getIssuer() || $issuer === $config->getIssuer();
    }

    protected function verifyAudience(OpenIdConnectConfiguration $config, array $payload): bool
    {
        $audiences = $payload['aud'] ?? [];
        if (!\is_array($audiences)) {
            $audiences = [$audiences];
        }

        $audiences[] = $payload['azp'] ?? null;

        foreach ($audiences as $audience) {
            if ($audience === $config->getClientId()) {
                return true;
            }
        }

        return false;
    }

    protected function verifyValidityTime(array $payload): bool
    {
        $currentTime = time();

        $issuedAt = $payload['iat'] ?? ($currentTime + 1);
        $expiresAt = $payload['exp'] ?? 0;

        return $issuedAt <= $currentTime && $expiresAt > $currentTime;
    }

    protected function loadKeys(OpenIdConnectConfiguration $config): ?JWKSet
    {
        if (!$config->getJwksUri()) {
            return null;
        }

        $jwksUri = $config->getJwksUri();

        $cacheKey = sprintf(
            'heptacom-admin-open-auth_jwks_%s',
            md5($jwksUri),
        );
        $cachedJwks = $this->cache->getItem($cacheKey);
        if (!$cachedJwks->isHit()) {
            try {
                $uri = new Uri($jwksUri);

                $request = new Request('GET', $uri);
                OpenIdConnectRequestHelper::prepareRequest($request);

                $response = $this->oidcHttpClient->sendRequest($request);
                OpenIdConnectRequestHelper::verifyRequestSuccess($request, $response);

                $cachedJwks->set((string) $response->getBody());
                $cachedJwks->expiresAfter(self::CACHE_TTL);
                $this->cache->save($cachedJwks);
            } catch (ClientExceptionInterface $e) {
                throw new OpenIdConnectException(
                    'Retrieving JWK-Set for token signature verification failed: ' . $e->getMessage()
                );
            }
        }

        $keys = JWKSet::createFromJson($cachedJwks->get());

        return $keys->count() === 0 ? null : $keys;
    }
}
