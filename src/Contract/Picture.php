<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Contract;

use Shopware\Core\Framework\Struct\Struct;
use Shopware\Core\Framework\Util\Hasher;

final class Picture extends Struct
{
    private string $id;
    private string $content = '';

    public string $fileExtension = '';

    public function getId(): string
    {
        return $this->id;
    }

    public function getContent(): string
    {
        return \base64_decode($this->content);
    }

    public function setContent(string $content): void
    {
        $this->id = Hasher::hash($content, 'xxh128');
        $this->content = \base64_encode($content);
    }
}
