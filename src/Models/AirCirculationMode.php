<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum AirCirculationMode: int
{
    case UNKNOWN = -1;
    case UNAVAILABLE = 0;
    case EXTERNAL = 1;
    case INTERNAL = 2;
}
