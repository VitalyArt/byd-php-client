<?php

declare(strict_types=1);

namespace Byd\ApiClient\Enum;

enum WatchEndpoint: string
{
    case SERVER_TIME = '/watch/login/getServerCurrentTime';
    case CREATE_QR_CODE = '/watch/login/create/qrcode';
    case CHECK_QR_CODE = '/watch/login/check/qrcode';
    case GAIN_TOKEN = '/watch/login/gain/token';
    case GAIN_VEHICLE = '/watch/login/gain/vehicle';
    case GAIN_BLUETOOTH = '/watch/login/gain/bluetooth';
    case LOGOUT = '/watch/watch/logoutWatch';
}
