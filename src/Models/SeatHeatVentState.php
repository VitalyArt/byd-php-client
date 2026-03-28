<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum SeatHeatVentState: int
{
    case UNKNOWN = -1;
    case NO_DATA = 0;
    case OFF = 1;
    case LOW = 2;
    case HIGH = 3;

    /**
     * Return the command level value for seat-climate control commands.
     * The BYD API uses an inverted scale:
     * Status HIGH(3) → Command 1 (most powerful)
     * Status LOW(2)  → Command 2
     * Status OFF(1)  → Command 3 (off)
     * Status NO_DATA(0) / UNKNOWN(-1) → Command 0 (no action)
     */
    public function toCommandLevel(): int
    {
        return match($this) {
            self::HIGH => 1,
            self::LOW => 2,
            self::OFF => 3,
            default => 0,
        };
    }
}
