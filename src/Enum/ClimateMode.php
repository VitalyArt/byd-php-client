<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum ClimateMode: int
{
    case UNKNOWN = -1;
    case OFF = 0;
    case AUTO = 1;
    case MANUAL = 2;
}
