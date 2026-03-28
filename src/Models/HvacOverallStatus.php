<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum HvacOverallStatus: int
{
    case UNKNOWN = -1;
    case ON = 1;
    case OFF = 2;
}
