<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests\Models;

use Byd\ApiClient\Models\ChargingState;
use Byd\ApiClient\Models\ChargingStatus;
use PHPUnit\Framework\TestCase;

class ChargingStatusTest extends TestCase
{
    // Default values when data is missing

    public function testDefaultsWhenEmpty(): void
    {
        $status = new ChargingStatus([]);

        $this->assertSame(ChargingState::UNKNOWN, $status->getChargingState());
        $this->assertNull($status->getChargerPower());
        $this->assertNull($status->getChargerVoltage());
        $this->assertNull($status->getChargerCurrent());
        $this->assertNull($status->getBatteryCapacity());
        $this->assertNull($status->getBatteryVoltage());
        $this->assertNull($status->getBatteryCurrent());
        $this->assertNull($status->getBatteryTemperature());
        $this->assertNull($status->getBatterySOC());
        $this->assertNull($status->getChargingPower());
        $this->assertNull($status->getChargingTime());
        $this->assertNull($status->getRemainingTime());
        $this->assertNull($status->getMileageOfCharge());
        $this->assertNull($status->getMileageOfDay());
        $this->assertNull($status->getMileageOfWeek());
        $this->assertNull($status->getMileageOfMonth());
        $this->assertNull($status->getStartTime());
        $this->assertNull($status->getEndTime());
        $this->assertNull($status->getChargingPileName());
        $this->assertNull($status->getChargingPileSN());
        $this->assertNull($status->getChargingType());
        $this->assertNull($status->getChargingCost());
        $this->assertNull($status->getElectricPrice());
        $this->assertNull($status->getServiceFee());
        $this->assertNull($status->getTotalFee());
    }

    // ChargingState enum

    public function testChargingStateCharging(): void
    {
        $status = new ChargingStatus(['chargingState' => 1]);
        $this->assertSame(ChargingState::CHARGING, $status->getChargingState());
    }

    public function testChargingStateNotCharging(): void
    {
        $status = new ChargingStatus(['chargingState' => 0]);
        $this->assertSame(ChargingState::NOT_CHARGING, $status->getChargingState());
    }

    public function testChargingStateConnected(): void
    {
        $status = new ChargingStatus(['chargingState' => 15]);
        $this->assertSame(ChargingState::NOT_CONNECTED, $status->getChargingState());
    }

    public function testChargingStateUnknown(): void
    {
        $status = new ChargingStatus(['chargingState' => -1]);
        $this->assertSame(ChargingState::UNKNOWN, $status->getChargingState());
    }

    public function testChargingStateInvalidFallsBackToUnknown(): void
    {
        $status = new ChargingStatus(['chargingState' => 99]);
        $this->assertSame(ChargingState::UNKNOWN, $status->getChargingState());
    }

    // Numeric fields

    public function testChargerDataPopulation(): void
    {
        $status = new ChargingStatus([
            'chargerPower' => '7.4',
            'chargerVoltage' => '230.0',
            'chargerCurrent' => '32.0',
            'batteryCapacity' => '77.0',
            'batteryVoltage' => '400.0',
            'batteryCurrent' => '20.0',
            'batteryTemperature' => '25.0',
            'batterySOC' => '85.0',
            'chargingPower' => '7.2',
            'chargingTime' => '120',
            'remainingTime' => '60',
        ]);

        $this->assertSame(7.4, $status->getChargerPower());
        $this->assertSame(230.0, $status->getChargerVoltage());
        $this->assertSame(32.0, $status->getChargerCurrent());
        $this->assertSame(77.0, $status->getBatteryCapacity());
        $this->assertSame(400.0, $status->getBatteryVoltage());
        $this->assertSame(20.0, $status->getBatteryCurrent());
        $this->assertSame(25.0, $status->getBatteryTemperature());
        $this->assertSame(85.0, $status->getBatterySOC());
        $this->assertSame(7.2, $status->getChargingPower());
        $this->assertSame(120, $status->getChargingTime());
        $this->assertSame(60, $status->getRemainingTime());
    }

    public function testMileageDataPopulation(): void
    {
        $status = new ChargingStatus([
            'mileageOfCharge' => '150.0',
            'mileageOfDay' => '50.0',
            'mileageOfWeek' => '300.0',
            'mileageOfMonth' => '1200.0',
        ]);

        $this->assertSame(150.0, $status->getMileageOfCharge());
        $this->assertSame(50.0, $status->getMileageOfDay());
        $this->assertSame(300.0, $status->getMileageOfWeek());
        $this->assertSame(1200.0, $status->getMileageOfMonth());
    }

    public function testChargingPileInfo(): void
    {
        $status = new ChargingStatus([
            'chargingPileName' => 'Fast Charger #1',
            'chargingPileSN' => 'SN123456',
            'chargingType' => 2,
        ]);

        $this->assertSame('Fast Charger #1', $status->getChargingPileName());
        $this->assertSame('SN123456', $status->getChargingPileSN());
        $this->assertSame(2, $status->getChargingType());
    }

    public function testFeeData(): void
    {
        $status = new ChargingStatus([
            'chargingCost' => '5.50',
            'electricPrice' => '0.25',
            'serviceFee' => '0.50',
            'totalFee' => '6.00',
        ]);

        $this->assertSame(5.50, $status->getChargingCost());
        $this->assertSame(0.25, $status->getElectricPrice());
        $this->assertSame(0.50, $status->getServiceFee());
        $this->assertSame(6.00, $status->getTotalFee());
    }

    // Timestamps

    public function testStartTimeInSeconds(): void
    {
        $ts = 1700000000;
        $status = new ChargingStatus(['startTime' => $ts]);
        $this->assertNotNull($status->getStartTime());
        $this->assertSame($ts, $status->getStartTime()->getTimestamp());
    }

    public function testEndTimeInMilliseconds(): void
    {
        $tsMs = 1700000000000;
        $status = new ChargingStatus(['endTime' => $tsMs]);
        $this->assertNotNull($status->getEndTime());
        $this->assertSame(1700000000, $status->getEndTime()->getTimestamp());
    }

    // getRaw

    public function testGetRaw(): void
    {
        $data = ['chargingState' => 1, 'chargerPower' => 7.4];
        $status = new ChargingStatus($data);
        $this->assertSame($data, $status->getRaw());
    }
}
