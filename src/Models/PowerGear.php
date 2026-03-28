<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum PowerGear: int
{
    case UNKNOWN = -1;
    case OFF = 1;
    case ON = 3;
}
