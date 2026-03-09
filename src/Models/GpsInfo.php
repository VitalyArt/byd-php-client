<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * GPS information for a vehicle.
 */
class GpsInfo extends BaseModel
{
    private ?float $latitude = null;
    private ?float $longitude = null;
    private ?float $altitude = null;
    private ?float $speed = null;
    private ?float $heading = null;
    private ?float $direction = null;
    private ?DateTimeInterface $timestamp = null;
    private ?string $positionType = null;

    /**
     * @param array<string, mixed> $data
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->latitude = isset($data['data']['latitude']) ? (float) $data['data']['latitude'] : null;
        $this->longitude = isset($data['data']['longitude']) ? (float) $data['data']['longitude'] : null;
        $this->altitude = isset($data['data']['altitude']) ? (float) $data['data']['altitude'] : null;
        $this->speed = isset($data['data']['speed']) ? (float) $data['data']['speed'] : null;
        $this->heading = isset($data['data']['heading']) ? (float) $data['data']['heading'] : null;
        $this->direction = isset($data['data']['direction']) ? (float) $data['data']['direction'] : null;

        if (isset($data['data']['gpsTimeStamp'])) {
            $this->timestamp = $this->parseTimestamp($data['data']['gpsTimeStamp']);
        }

        $this->positionType = isset($data['data']['positionType']) ? (string) $data['data']['positionType'] : null;
    }

    private function parseTimestamp($timestamp): ?DateTimeInterface
    {
        if ($timestamp === null) {
            return null;
        }

        $ts = (int) $timestamp;
        // Threshold to distinguish seconds from milliseconds.
        if ($ts >= 1000000000000) {
            $ts = (int) ($ts / 1000);
        }

        return DateTimeImmutable::createFromFormat('U', (string) $ts);
    }

    /**
     * Check if GPS data has meaningful content.
     *
     * @param array<string, mixed> $gpsInfo
     */
    public static function isGpsInfoReady(array $gpsInfo): bool
    {
        return !empty($gpsInfo) && array_keys($gpsInfo) !== ['requestSerial'];
    }

    // Getters
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function getAltitude(): ?float
    {
        return $this->altitude;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function getHeading(): ?float
    {
        return $this->heading;
    }

    public function getDirection(): ?float
    {
        return $this->direction;
    }

    public function getTimestamp(): ?DateTimeInterface
    {
        return $this->timestamp;
    }

    public function getPositionType(): ?string
    {
        return $this->positionType;
    }
}
