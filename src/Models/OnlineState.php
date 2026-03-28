<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum OnlineState: int
{
    case UNKNOWN = -1;
    case ONLINE = 1;
    case OFFLINE = 2;
}
