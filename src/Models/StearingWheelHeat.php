<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum StearingWheelHeat: int
{
    case ON = -1;  // confirmed live: -1 means ON
    case OFF = 1;

    /**
     * Return the command level value for seat-climate commands.
     * Command scale: 1 = on, 3 = off.
     */
    public function toCommandLevel(): int
    {
        return $this === self::ON ? 1 : 3;
    }
}
