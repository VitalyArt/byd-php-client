<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests\Models;

use Byd\ApiClient\Models\GpsInfo;
use PHPUnit\Framework\TestCase;

class GpsInfoTest extends TestCase
{
    // Default values when data is missing

    public function testDefaultsWhenEmpty(): void
    {
        $gps = new GpsInfo([]);

        $this->assertNull($gps->getLatitude());
        $this->assertNull($gps->getLongitude());
        $this->assertNull($gps->getAltitude());
        $this->assertNull($gps->getSpeed());
        $this->assertNull($gps->getHeading());
        $this->assertNull($gps->getDirection());
        $this->assertNull($gps->getTimestamp());
        $this->assertNull($gps->getPositionType());
    }

    // GPS data is nested under 'data' key

    public function testGpsDataPopulation(): void
    {
        $gps = new GpsInfo([
            'data' => [
                'latitude' => '52.3676',
                'longitude' => '4.9041',
                'altitude' => '5.0',
                'speed' => '60.5',
                'heading' => '180.0',
                'direction' => '180.0',
                'positionType' => 'GPS',
            ],
        ]);

        $this->assertSame(52.3676, $gps->getLatitude());
        $this->assertSame(4.9041, $gps->getLongitude());
        $this->assertSame(5.0, $gps->getAltitude());
        $this->assertSame(60.5, $gps->getSpeed());
        $this->assertSame(180.0, $gps->getHeading());
        $this->assertSame(180.0, $gps->getDirection());
        $this->assertSame('GPS', $gps->getPositionType());
    }

    public function testGpsDataMissingNestedKey(): void
    {
        $gps = new GpsInfo(['latitude' => '52.3676']);
        $this->assertNull($gps->getLatitude());
    }

    // Timestamps

    public function testTimestampInSeconds(): void
    {
        $ts = 1700000000;
        $gps = new GpsInfo([
            'data' => ['gpsTimeStamp' => $ts],
        ]);
        $this->assertNotNull($gps->getTimestamp());
        $this->assertSame($ts, $gps->getTimestamp()->getTimestamp());
    }

    public function testTimestampInMilliseconds(): void
    {
        $tsMs = 1700000000000;
        $gps = new GpsInfo([
            'data' => ['gpsTimeStamp' => $tsMs],
        ]);
        $this->assertNotNull($gps->getTimestamp());
        $this->assertSame(1700000000, $gps->getTimestamp()->getTimestamp());
    }

    // isGpsInfoReady

    public function testIsGpsInfoReadyEmpty(): void
    {
        $this->assertFalse(GpsInfo::isGpsInfoReady([]));
    }

    public function testIsGpsInfoReadyWithOnlyRequestSerial(): void
    {
        $this->assertFalse(GpsInfo::isGpsInfoReady(['requestSerial' => 'abc123']));
    }

    public function testIsGpsInfoReadyWithData(): void
    {
        $this->assertTrue(GpsInfo::isGpsInfoReady([
            'data' => ['latitude' => '52.3676', 'longitude' => '4.9041'],
        ]));
    }

    public function testIsGpsInfoReadyWithRequestSerialAndData(): void
    {
        $this->assertTrue(GpsInfo::isGpsInfoReady([
            'requestSerial' => 'abc123',
            'data' => ['latitude' => '52.3676'],
        ]));
    }

    // getRaw

    public function testGetRaw(): void
    {
        $data = ['data' => ['latitude' => '52.3676']];
        $gps = new GpsInfo($data);
        $this->assertSame($data, $gps->getRaw());
    }

    // Full payload test

    public function testFullPayload(): void
    {
        $gps = new GpsInfo([
            'data' => [
                'latitude' => 52.3676,
                'longitude' => 4.9041,
                'altitude' => 5.0,
                'speed' => 0.0,
                'heading' => 90.0,
                'direction' => 90.0,
                'gpsTimeStamp' => 1700000000,
                'positionType' => 'GPS',
            ],
        ]);

        $this->assertSame(52.3676, $gps->getLatitude());
        $this->assertSame(4.9041, $gps->getLongitude());
        $this->assertSame(5.0, $gps->getAltitude());
        $this->assertSame(0.0, $gps->getSpeed());
        $this->assertSame(90.0, $gps->getHeading());
        $this->assertSame(90.0, $gps->getDirection());
        $this->assertNotNull($gps->getTimestamp());
        $this->assertSame('GPS', $gps->getPositionType());
    }
}
