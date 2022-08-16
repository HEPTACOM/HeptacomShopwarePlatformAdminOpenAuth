<?php
declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Service\Provider;

use Heptacom\AdminOpenAuth\Component\Provider\DefaultInjectingEntityRepositoryDecorator;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class GoogleCloudClientEntityRepository extends DefaultInjectingEntityRepositoryDecorator
{
    protected function injectDefaults(array $payload, Context $context): array
    {
        $ids = \array_values(\array_column($payload, 'id'));

        if (!empty($ids)) {
            $ids = $this->searchIds(new Criteria($ids), $context)->getIds();
        }

        foreach ($payload as &$item) {
            if (($item['provider'] ?? '') !== GoogleCloudProvider::PROVIDER_NAME) {
                continue;
            }

            if (\array_key_exists('id', $item) && \in_array($item['id'], $ids, true)) {
                continue;
            }

            $item['storeUserToken'] = $item['storeUserToken'] ?? true;
            $item['login'] = $item['login'] ?? true;
            $item['connect'] = $item['connect'] ?? true;
        }

        return $payload;
    }
}
