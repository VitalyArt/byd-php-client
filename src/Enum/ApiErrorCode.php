<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum ApiErrorCode: int
{
    case GENERIC = 1001;
    case ENDPOINT_NOT_SUPPORTED = 1004;
    case VEHICLE_NOT_SUPPORTED = 1005;
    case REMOTE_CONTROL_UNAVAILABLE = 1009;
    case SESSION_EXPIRED = 1017;
    case INVALID_CONTROL_PIN = 5005;
    case CONTROL_PIN_LOCKED = 5006;
    case CONTROL_PIN_REQUIRED = 5011;
}
