<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * Seat heating / ventilation / steering wheel heat level.
 */
class SeatHeatVentState extends AbstractEnum
{
    public const UNKNOWN = -1;
    public const NO_DATA = 0; // API has no data for this seat right now
    public const OFF = 1; // feature exists, currently inactive
    public const LOW = 2;
    public const HIGH = 3;

    /**
     * Return the value to send in a set_seat_climate() command.
     *
     * The BYD API uses an inverted scale for commands compared to
     * status readings:
     * Status  →  Command
     * HIGH=3  →  1 (most powerful)
     * LOW=2   →  2 (least powerful)
     * OFF=1   →  3 (off)
     *
     * NO_DATA (0) and UNKNOWN (-1) both map to 0
     * (no action / feature absent).
     */
    public static function toCommandLevel(int $value): int
    {
        // Command scale (for controlParamsMap) is *inverted* compared to
        // the status scale: 1 = high, 2 = low, 3 = off.
        $map = [
            self::UNKNOWN => 0, // no action
            self::NO_DATA => 0, // no action
            self::OFF => 3, // off
            self::LOW => 2, // low
            self::HIGH => 1, // high
        ];

        return $map[$value] ?? 0;
    }
}
