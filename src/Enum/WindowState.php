<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum WindowState: int
{
    case UNKNOWN = -1;
    case CLOSED = 1;
    case OPEN = 2;
}
