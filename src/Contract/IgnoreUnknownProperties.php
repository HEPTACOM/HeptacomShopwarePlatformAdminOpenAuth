<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Struct\AssignArrayTrait;

trait IgnoreUnknownProperties
{
    use AssignArrayTrait {
        AssignArrayTrait::assign as baseAssign;
    }

    public function assign(array $options)
    {
        foreach ($options as $key => $value) {
            if (!\property_exists($this, $key)) {
                unset($options[$key]);
            }
        }

        return $this->baseAssign($options);
    }
}
