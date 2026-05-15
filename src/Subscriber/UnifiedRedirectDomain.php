<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Subscriber;

use Heptacom\AdminOpenAuth\Database\LoginDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWriteEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\InsertCommand;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final readonly class UnifiedRedirectDomain implements EventSubscriberInterface
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            EntityWriteEvent::class => 'addOriginUrl',
        ];
    }

    public function addOriginUrl(EntityWriteEvent $event): void
    {
        $commands = $event->getCommandsForEntity(LoginDefinition::ENTITY_NAME);

        foreach ($commands as $command) {
            if (!$command instanceof InsertCommand) {
                continue;
            }

            try {
                $payload = \json_decode($command->getPayload()['payload'] ?? '{}', true, 512, \JSON_THROW_ON_ERROR);

                if (!\is_array($payload)) {
                    // This should never happen, as the DAL already decodes the payload and it's a required field
                    throw new \RuntimeException('Decoded payload is not an array', 1775050147);
                }

                $payload['originUrl'] = $this->requestStack->getMainRequest()?->getUriForPath('/');

                $command->addPayload('payload', \json_encode($payload, \JSON_THROW_ON_ERROR));
            } catch (\JsonException) {
                // This should never happen, as the DAL already decodes the payload
                throw new \RuntimeException('Failed to decode or encode JSON payload', 1775050166);
            }
        }
    }
}
