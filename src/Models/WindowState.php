<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Window open/closed state.
 */
class WindowState extends AbstractEnum
{
    public const UNKNOWN = -1;
    public const CLOSED = 1; // confirmed
    public const OPEN = 2; // assumed from BYD SDK: BODYWORK_STATE_OPEN
}
