<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

final class JumpCloudClientEntityWriter extends Saml2ClientEntityWriter
{
    protected function getProvider(): string
    {
        return JumpCloudServiceProvider::PROVIDER_NAME;
    }

    protected function injectDefaults(array $payload): array
    {
        $payload['storeUserToken'] = false;
        $payload['login'] ??= true;
        $payload['connect'] ??= true;

        return $payload;
    }
}
