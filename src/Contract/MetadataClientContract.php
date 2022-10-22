<?php

declare(strict_types = 1);


namespace Heptacom\AdminOpenAuth\Contract;

/**
 * If implemented, the client can provide metadata which are made publicly available.
 */
interface MetadataClientContract
{
    /**
     * Returns the mime type of the requested metadata
     * @return string
     */
    public function getMetadataType(): string;

    /**
     * Returns the metadata for this client, to be exposed
     */
    public function getMetadata(): string;
}
