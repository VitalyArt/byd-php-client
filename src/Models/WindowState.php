<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum WindowState: int
{
    case UNKNOWN = -1;
    case CLOSED = 1;
    case OPEN = 2;
}
