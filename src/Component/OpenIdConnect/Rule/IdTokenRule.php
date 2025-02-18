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

class IdTokenRule extends RuleContract
{
    use JMESPathValidation;

    public const RULE_NAME = 'heptacomAdminOpenAuthIdToken';

    public function __construct(
        protected ?string $jmesPathExpression = null,
    ) {
        parent::__construct();
    }

    public function matchRule(OAuthRuleScope $scope): bool
    {
        if ($this->jmesPathExpression === null) {
            return false;
        }

        $client = $scope->getClient();
        $user = $scope->getUser();
        $oidcExtension = $user->getExtensionOfType('oidcData', ArrayStruct::class);
        $idTokenPayload = $oidcExtension?->get('idTokenPayload');

        if (!$client instanceof OpenIdConnectClient || $idTokenPayload === null) {
            return false;
        }

        return $this->matchExpression((string) $this->jmesPathExpression, $idTokenPayload);
    }

    public function getConstraints(): array
    {
        return [
            'jmesPathExpression' => RuleConstraints::string(),
        ];
    }

    public function getConfig(): ?RuleConfig
    {
        return (new RuleConfig())
            ->stringField('jmesPathExpression');
    }
}
