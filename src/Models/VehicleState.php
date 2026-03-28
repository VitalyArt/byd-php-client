<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum VehicleState: int
{
    case UNKNOWN = -1;
    case OFF = 0;
    case ON = 2;
}
