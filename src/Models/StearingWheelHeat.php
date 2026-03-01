<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Steering wheel heating level.
 */
class StearingWheelHeat extends AbstractEnum
{
    public const ON = -1; // makes no sense, but tested live.
    public const OFF = 1;

    /**
     * Return the value to send in a seat-climate command.
     *
     * Command scale: 1 = on, 3 = off.
     */
    public static function toCommandLevel(int $value): int
    {
        return $value === self::ON ? 1 : 3;
    }
}
