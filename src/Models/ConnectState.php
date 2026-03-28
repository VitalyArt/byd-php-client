<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum ConnectState: int
{
    case UNKNOWN = -1;
    case DISCONNECTED = 0;
    case CONNECTED = 1;
}
