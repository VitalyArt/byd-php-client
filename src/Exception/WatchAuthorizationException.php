<?php

declare(strict_types=1);

namespace Byd\ApiClient\Exception;

use Byd\ApiClient\Enum\WatchQrStatus;

final class WatchAuthorizationException extends BydException
{
    public function __construct(public readonly WatchQrStatus $status)
    {
        parent::__construct('Watch authorization did not complete successfully: '.$status->name.'.');
    }
}
