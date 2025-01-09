<?php

declare(strict_types=1);

namespace Heptacom\AdminOpenAuth\Exception;

use Shopware\Core\Framework\ShopwareHttpException;

class UserMismatchException extends ShopwareHttpException
{
    public function __construct(?\Throwable $previous = null)
    {
        parent::__construct('The given user is not allowed to accept this confirmation', [], $previous);
    }

    public function getErrorCode(): string
    {
        return 'HEPTACOM_ADMIN_OPEN_AUTH__USER_MISMATCH';
    }
}
