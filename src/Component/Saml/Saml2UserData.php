<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Component\Saml;

use Shopware\Core\Framework\Struct\Struct;

class Saml2UserData extends Struct
{
    public array $roles = [];
}
