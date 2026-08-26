<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models\Control;

/**
 * Command values accepted by /control/remoteControl.
 */
enum RemoteCommand: string
{
    case LOCK = 'LOCKDOOR';
    case UNLOCK = 'OPENDOOR';
    case START_CLIMATE = 'OPENAIR';
    case STOP_CLIMATE = 'CLOSEAIR';
    case SCHEDULE_CLIMATE = 'BOOKINGAIR';
    case FIND_CAR = 'FINDCAR';
    case FLASH_LIGHTS = 'FLASHLIGHTNOWHISTLE';
    case CLOSE_WINDOWS = 'CLOSEWINDOW';
    case OPEN_WINDOWS = 'OPENWINDOW';
    case SEAT_CLIMATE = 'VENTILATIONHEATING';
    case BATTERY_HEAT = 'BATTERYHEAT';
    case OPEN_TRUNK = 'OPENTRUNK';
    case CLOSE_TRUNK = 'CLOSETRUNK';
}
