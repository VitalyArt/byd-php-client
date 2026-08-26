<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum ChargingConnectionState: int
{
    case UNKNOWN = -1;
    case DISCONNECTED = 0;
    case CONNECTED = 1;
}
