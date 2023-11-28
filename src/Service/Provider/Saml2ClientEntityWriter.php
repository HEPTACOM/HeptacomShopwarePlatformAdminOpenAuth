<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Provider\DefaultInjectingEntityWriterDecorator;

class Saml2ClientEntityWriter extends DefaultInjectingEntityWriterDecorator
{
    protected function getProvider(): string
    {
        return Saml2ServiceProvider::PROVIDER_NAME;
    }

    protected function injectDefaults(array $payload): array
    {
        $payload['storeUserToken'] = false;
        $payload['login'] ??= true;
        $payload['connect'] ??= true;

        return $payload;
    }
}
