<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Door/trunk open/closed state.
 */
class DoorOpenState extends AbstractEnum
{
    public const UNKNOWN = -1;
    public const CLOSED = 0;
    public const OPEN = 1;
}
