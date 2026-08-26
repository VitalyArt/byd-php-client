<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum SeatClimateLevel: int
{
    case UNKNOWN = -1;
    case NO_DATA = 0;
    case OFF = 1;
    case LOW = 2;
    case HIGH = 3;

    public function commandValue(): int
    {
        return match ($this) {
            self::HIGH => 1, self::LOW => 2, self::OFF => 3, default => 0
        };
    }
}
