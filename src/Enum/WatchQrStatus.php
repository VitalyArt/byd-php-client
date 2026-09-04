<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

use function in_array;

enum WatchQrStatus: int
{
    case UNKNOWN = -1;
    case WAITING_FOR_SCAN = 0;
    case WAITING_FOR_CONFIRMATION = 1;
    case APPROVED = 2;
    case INVALIDATED = 3;
    case EXPIRED = 4;

    public function isTerminal(): bool
    {
        return in_array($this, [self::APPROVED, self::INVALIDATED, self::EXPIRED], true);
    }
}
