<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum OtaUpgradeStatus: int
{
    case UNKNOWN = 0;
    case UPGRADE_AVAILABLE = 1;
    case UPGRADE_BOOKING_AVAILABLE = 2;
    case UPGRADING = 3;
    case UPGRADE_READY = 4;
    case BOOKED = 5;
    case BOOKING_CANCELLED = 6;
}
