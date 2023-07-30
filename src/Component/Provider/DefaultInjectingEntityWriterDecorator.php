<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Provider;

use Heptacom\AdminOpenAuth\Database\ClientDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Write\Command\WriteCommandQueue;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityWriteGatewayInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\EntityWriterInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteContext;
use Shopware\Core\Framework\DataAbstractionLayer\Write\WriteResult;

abstract class DefaultInjectingEntityWriterDecorator implements EntityWriterInterface
{
    public function __construct(
        private readonly EntityWriterInterface $decorated,
        private readonly EntityWriteGatewayInterface $writeGateway
    ) {
    }

    public function sync(array $operations, WriteContext $context): WriteResult
    {
        return $this->decorated->sync($operations, $context);
    }

    public function upsert(EntityDefinition $definition, array $rawData, WriteContext $writeContext): array
    {
        if ($definition->getEntityName() === ClientDefinition::ENTITY_NAME) {
            $provider = $this->getProvider();

            foreach ($rawData as &$raw) {
                if ($raw['provider'] !== $provider) {
                    continue;
                }

                $existence = $this->writeGateway->getExistence($definition, ['id' => $raw['id']], $raw, new WriteCommandQueue());

                if (!$existence->exists()) {
                    $raw = $this->injectDefaults($raw);
                }
            }
        }

        return $this->decorated->upsert($definition, $rawData, $writeContext);
    }

    public function insert(EntityDefinition $definition, array $rawData, WriteContext $writeContext)
    {
        if ($definition->getEntityName() === ClientDefinition::ENTITY_NAME) {
            $provider = $this->getProvider();

            foreach ($rawData as &$raw) {
                if ($raw['provider'] !== $provider) {
                    continue;
                }

                $raw = $this->injectDefaults($raw);
            }
        }

        return $this->decorated->insert($definition, $rawData, $writeContext);
    }

    public function update(EntityDefinition $definition, array $rawData, WriteContext $writeContext)
    {
        return $this->decorated->update($definition, $rawData, $writeContext);
    }

    public function delete(EntityDefinition $definition, array $ids, WriteContext $writeContext): WriteResult
    {
        return $this->decorated->delete($definition, $ids, $writeContext);
    }

    abstract protected function getProvider(): string;

    abstract protected function injectDefaults(array $payload): array;
}
