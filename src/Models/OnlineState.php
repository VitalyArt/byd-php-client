<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Vehicle online/offline state.
 */
class OnlineState extends AbstractEnum
{
    public const UNKNOWN = -1;
    public const ONLINE = 1;
    public const OFFLINE = 2;
}
