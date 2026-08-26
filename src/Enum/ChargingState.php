<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum ChargingState: int
{
    case UNKNOWN = -1;
    case NOT_CHARGING = 0;
    case CHARGING = 1;
    case NOT_CONNECTED = 15;
}
