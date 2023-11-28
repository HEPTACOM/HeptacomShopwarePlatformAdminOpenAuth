<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Struct\Struct;

final class RedirectBehaviour extends Struct
{
    public const DEFAULT_EXPECT_STATE = false;

    public const DEFAULT_CODE_KEY = 'code';

    public const DEFAULT_STATE_KEY = 'state';

    public function __construct(
        public bool $expectState = self::DEFAULT_EXPECT_STATE,
        public string $codeKey = self::DEFAULT_CODE_KEY,
        public string $stateKey = self::DEFAULT_STATE_KEY,
        public ?string $redirectUri = null
    ) {
    }
}
