<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum HvacWindMode: int
{
    case UNKNOWN = -1;
    case OFF = 0;
    case FACE = 1;
    case FACE_FOOT = 2;
    case FOOT = 3;
    case FOOT_DEFROST = 4;
    case DEFROST = 5;
}
