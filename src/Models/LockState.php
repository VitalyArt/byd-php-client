<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Door lock state.
 */
class LockState extends AbstractEnum
{
    public const UNKNOWN = -1;
    public const UNAVAILABLE = 0; // BYD API returns 0 when state is unavailable
    public const UNLOCKED = 1; // confirmed
    public const LOCKED = 2; // confirmed
}
