<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum LockState: int
{
    case UNKNOWN = -1;
    case UNAVAILABLE = 0;
    case UNLOCKED = 1;
    case LOCKED = 2;
}
