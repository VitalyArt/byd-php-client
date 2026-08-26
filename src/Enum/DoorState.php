<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum DoorState: int
{
    case UNKNOWN = -1;
    case CLOSED = 0;
    case OPEN = 1;
}
