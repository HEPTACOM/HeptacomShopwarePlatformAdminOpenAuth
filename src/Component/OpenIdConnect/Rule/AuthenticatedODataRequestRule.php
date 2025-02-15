<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect\Rule;

use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\User;

class AuthenticatedODataRequestRule extends AuthenticatedRequestRule
{
    public const RULE_NAME = 'heptacomAdminOpenAuthAuthenticatedODataRequest';

    protected function executeAuthenticatedRequest(OpenIdConnectClient $client, User $user, OAuthRuleScope $scope): bool
    {
        $originalRequestUrl = $this->requestUrl;

        do {
            $nextUrl = null;

            $response = $this->performRequest($client, $user, $scope->getLogger());
            $validationResult = $this->validateResponse($response);

            if ($response !== null && !$validationResult) {
                $nextUrl = $this->getODataNextUrl($response);

                if ($nextUrl !== null) {
                    $this->requestUrl = $nextUrl;
                }
            }
        } while (!$validationResult && $nextUrl !== null);

        $this->requestUrl = $originalRequestUrl;

        return $validationResult;
    }

    private function getODataNextUrl(string $response): ?string
    {
        try {
            $odataResponse = \json_decode($response, true, 512, \JSON_THROW_ON_ERROR);
            return $odataResponse['@odata.nextLink'] ?? null;
        } catch (\JsonException $e) {
            return null;
        }
    }
}
