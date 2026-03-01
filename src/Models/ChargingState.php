<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Charging state indicator.
 */
class ChargingState extends AbstractEnum
{
    public const UNKNOWN = -1;
    public const NOT_CHARGING = 0;
    public const CHARGING = 1;
    public const CONNECTED = 15; // charging gun connected, not actively charging
}
