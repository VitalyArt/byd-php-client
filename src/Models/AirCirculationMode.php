<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Air circulation mode.
 */
class AirCirculationMode extends AbstractEnum
{
    public const UNKNOWN = -1;
    public const UNAVAILABLE = 0;
    public const EXTERNAL = 1;
    public const INTERNAL = 2;
}
