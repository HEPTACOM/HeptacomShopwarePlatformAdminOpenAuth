<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\OpenIdConnect\Rule;

use Heptacom\AdminOpenAuth\Component\Provider\OpenIdConnectClient;
use Heptacom\AdminOpenAuth\Contract\OAuthRuleScope;
use Heptacom\AdminOpenAuth\Contract\RuleContract;
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

        $scope->getLogger()->info('Available root keys for id-token', [
            'id_token_keys' => \array_keys($idTokenPayload),
            'log_code' => '1747412312',
        ]);

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
