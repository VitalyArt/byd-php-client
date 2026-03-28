<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum HvacWindPosition: int
{
    case UNKNOWN = -1;
    case OFF = 0;
    case POSITION_1 = 1;
    case POSITION_2 = 2;
    case POSITION_3 = 3;
    case POSITION_4 = 4;
    case POSITION_5 = 5;
    case POSITION_6 = 6;
    case POSITION_7 = 7;
}
