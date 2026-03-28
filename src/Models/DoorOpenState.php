<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum DoorOpenState: int
{
    case UNKNOWN = -1;
    case CLOSED = 0;
    case OPEN = 1;
}
