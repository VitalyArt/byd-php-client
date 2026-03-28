<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum TirePressureUnit: int
{
    case UNKNOWN = -1;
    case BAR = 1;
    case PSI = 2;
    case KPA = 3;
}
