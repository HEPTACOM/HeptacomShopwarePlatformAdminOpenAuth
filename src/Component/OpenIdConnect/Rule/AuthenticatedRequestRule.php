<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect\Rule;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use Heptacom\AdminOpenAuth\Component\OpenIdConnect\OpenIdConnectRequestHelper;
use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleContract;
use Heptacom\AdminOpenAuth\Contract\User;
use JmesPath\Env as JmesPath;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Rule\RuleConfig;
use Shopware\Core\Framework\Rule\RuleConstraints;
use Shopware\Core\Framework\Struct\ArrayStruct;

class AuthenticatedRequestRule extends RuleContract
{
    public const RULE_NAME = 'heptacomAdminOpenAuthAuthenticatedRequest';

    public const REQUEST_TIMEOUT = 5.0;

    private static ?ClientInterface $httpClient = null;

    public function __construct(
        protected ?string $requestUrl = null,
        protected ?string $jmesPathExpression = null,
    ) {
        parent::__construct();
    }

    public function matchRule(OAuthRuleScope $scope): bool
    {
        if ($this->requestUrl === null || $this->jmesPathExpression === null) {
            return false;
        }

        $client = $scope->getClient();
        $user = $scope->getUser();

        if (!$client instanceof OpenIdConnectClient || $user->tokenPair?->accessToken === null) {
            return false;
        }

        $response = $this->performRequest($client, $user, $scope->getLogger());

        return $this->validateResponse($response);
    }

    public function getConstraints(): array
    {
        return [
            'requestUrl' => RuleConstraints::string(),
            'jmesPathExpression' => RuleConstraints::string(),
        ];
    }

    public function getConfig(): ?RuleConfig
    {
        return (new RuleConfig())
            ->stringField('requestUrl')
            ->stringField('jmesPathExpression');
    }

    protected function validateExpressionResult($evaluatedExpression): bool
    {
        return match (\gettype($evaluatedExpression)) {
            'NULL' => false,
            'boolean' => $evaluatedExpression,
            'integer' => $evaluatedExpression !== 0,
            'double' => $evaluatedExpression !== 0.0,
            'string' => $evaluatedExpression !== '',
            'array' => \count($evaluatedExpression) > 0,
            default => false,
        };
    }

    private function getHttpClient(): ClientInterface
    {
        if (self::$httpClient === null) {
            self::$httpClient = new Client([
                'protocols' => ['https'],
                'verify' => true,
                'timeout' => self::REQUEST_TIMEOUT,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
        }

        return self::$httpClient;
    }

    private function performRequest(OpenIdConnectClient $client, User $user, LoggerInterface $logger): ?string
    {
        $requestUrl = (string) $this->requestUrl;

        try {
            return $this->getCachedResponse($user, $requestUrl);
        } catch (CachedRequestNotFoundException $e) {
            // ignore
        }

        // perform request
        try {
            $requestUrl = (string) $this->requestUrl;
            $uri = new Uri($requestUrl);

            if ($uri->getScheme() !== 'https') {
                throw new AuthenticatedRequestException($requestUrl, 'Only HTTPS requests are allowed', 1702992682);
            }

            $token = $user->tokenPair;
            if ($token === null) {
                throw new AuthenticatedRequestException($requestUrl, 'No token found', 1702992684);
            }

            $request = $client->authorizeRequest(new Request('GET', $uri), $token);
            $response = $this->getHttpClient()->sendRequest($request);

            OpenIdConnectRequestHelper::verifyRequestSuccess($request, $response);

            $response = (string) $response->getBody();
        } catch (\Throwable $e) {
            $logger->error(
                'Failed to perform authenticated request',
                [
                    'log_code' => 1702993083,
                    'request_url' => $requestUrl,
                    'user' => $user->primaryKey,
                    'exception' => $e->getMessage(),
                    'exception_code' => $e->getCode(),
                ]
            );

            $response = null;
        }

        $this->cacheResponse($user, $requestUrl, $response);

        return $response;
    }

    private function getCachedResponse(User $user, string $requestUrl): ?string
    {
        /** @var ArrayStruct $userExtension */
        $userExtension = $user->getExtensionOfType(AuthenticatedRequestRule::class, ArrayStruct::class)
            ?? new ArrayStruct();
        $cachedRequests = $userExtension->get('requests') ?? [];

        if (\array_key_exists($requestUrl, $cachedRequests)) {
            return $cachedRequests[$requestUrl];
        }

        throw new CachedRequestNotFoundException($requestUrl);
    }

    private function cacheResponse(User $user, string $requestUrl, ?string $response): void
    {
        /** @var ArrayStruct $userExtension */
        $userExtension = $user->getExtensionOfType(AuthenticatedRequestRule::class, ArrayStruct::class)
            ?? new ArrayStruct();
        $cachedRequests = $userExtension->get('requests') ?? [];

        $cachedRequests[$requestUrl] = $response;

        $userExtension->set('requests', $cachedRequests);
        $user->addExtension(AuthenticatedRequestRule::class, $userExtension);
    }

    private function validateResponse(?string $response): bool
    {
        if ($response === null) {
            return false;
        }

        try {
            $evaluatedExpression = JmesPath::search(
                (string) $this->jmesPathExpression,
                json_decode($response, true, 512, \JSON_THROW_ON_ERROR)
            );

            return $this->validateExpressionResult($evaluatedExpression);
        } catch (\Throwable) {
            return false;
        }
    }
}
