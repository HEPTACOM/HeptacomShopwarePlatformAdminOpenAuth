<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Provider\DefaultInjectingEntityWriterDecorator;

final class MicrosoftAzureOidcClientEntityWriter extends DefaultInjectingEntityWriterDecorator
{
    protected function getProvider(): string
    {
        return MicrosoftAzureOidcProvider::PROVIDER_NAME;
    }

    protected function injectDefaults(array $payload): array
    {
        $payload['storeUserToken'] ??= true;
        $payload['login'] ??= true;
        $payload['connect'] ??= true;

        return $payload;
    }
}
