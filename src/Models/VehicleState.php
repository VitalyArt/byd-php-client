<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

enum VehicleState: int
{
    case UNKNOWN = -1;
    case STARTED = 0;
    case DRIVING = 1;
    case POWER_OFF = 2;

    public function isUnknown(): bool
    {
        return $this->value === self::UNKNOWN->value;
    }

    public function isPowerOff(): bool
    {
        return $this->value === self::POWER_OFF->value;
    }

    public function isPowerOn(): bool
    {
        return $this->value === self::DRIVING->value
            || $this->value === self::STARTED->value;
    }
}
