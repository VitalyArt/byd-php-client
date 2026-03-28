<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests\Models;

use Byd\ApiClient\Models\AirCirculationMode;
use Byd\ApiClient\Models\ChargingState;
use Byd\ApiClient\Models\ConnectState;
use Byd\ApiClient\Models\DoorOpenState;
use Byd\ApiClient\Models\OnlineState;
use Byd\ApiClient\Models\PowerGear;
use Byd\ApiClient\Models\SeatHeatVentState;
use Byd\ApiClient\Models\StearingWheelHeat;
use Byd\ApiClient\Models\TirePressureUnit;
use Byd\ApiClient\Models\VehicleRealtimeData;
use Byd\ApiClient\Models\VehicleState;
use Byd\ApiClient\Models\WindowState;
use PHPUnit\Framework\TestCase;

class VehicleRealtimeDataTest extends TestCase
{
    // Default values when data is missing

    public function testDefaultsWhenEmpty(): void
    {
        $data = new VehicleRealtimeData([]);

        $this->assertSame(OnlineState::UNKNOWN, $data->getOnlineState());
        $this->assertSame(ConnectState::UNKNOWN, $data->getConnectState());
        $this->assertSame(VehicleState::UNKNOWN, $data->getVehicleState());
        $this->assertSame(ChargingState::UNKNOWN, $data->getChargingState());
        $this->assertSame(ChargingState::UNKNOWN, $data->getChargeState());
        $this->assertSame(PowerGear::UNKNOWN, $data->getPowerGear());
        $this->assertSame(AirCirculationMode::UNKNOWN, $data->getAirRunState());
        $this->assertSame(TirePressureUnit::UNKNOWN, $data->getTirePressUnit());
        $this->assertNull($data->getElecPercent());
        $this->assertNull($data->getTempInCar());
        $this->assertNull($data->getTimestamp());
        $this->assertNull($data->getRequestSerial());
        $this->assertFalse($data->isOnline());
        $this->assertFalse($data->isCharging());
        $this->assertFalse($data->isAnyDoorOpen());
        $this->assertFalse($data->isAnyWindowOpen());
        $this->assertFalse($data->isVehicleOn());
        $this->assertFalse($data->isInteriorTempAvailable());
        $this->assertFalse($data->isSteeringWheelHeating());
        $this->assertNull($data->isLocked());
        $this->assertNull($data->isBatteryHeating());
    }

    // Connection & state population

    public function testOnlineStatePopulation(): void
    {
        $data = new VehicleRealtimeData(['onlineState' => 1]);
        $this->assertSame(OnlineState::ONLINE, $data->getOnlineState());
        $this->assertTrue($data->isOnline());

        $data = new VehicleRealtimeData(['onlineState' => 2]);
        $this->assertSame(OnlineState::OFFLINE, $data->getOnlineState());
        $this->assertFalse($data->isOnline());
    }

    public function testUnknownOnlineStateFallsBackToUnknown(): void
    {
        $data = new VehicleRealtimeData(['onlineState' => 99]);
        $this->assertSame(OnlineState::UNKNOWN, $data->getOnlineState());
    }

    public function testConnectStatePopulation(): void
    {
        $data = new VehicleRealtimeData(['connectState' => 1]);
        $this->assertSame(ConnectState::CONNECTED, $data->getConnectState());

        $data = new VehicleRealtimeData(['connectState' => 0]);
        $this->assertSame(ConnectState::DISCONNECTED, $data->getConnectState());
    }

    public function testVehicleStatePopulation(): void
    {
        $data = new VehicleRealtimeData(['vehicleState' => 2]);
        $this->assertSame(VehicleState::POWER_OFF, $data->getVehicleState());

        $data = new VehicleRealtimeData(['vehicleState' => 0]);
        $this->assertSame(VehicleState::STARTED, $data->getVehicleState());

        $data = new VehicleRealtimeData(['vehicleState' => 1]);
        $this->assertSame(VehicleState::DRIVING, $data->getVehicleState());
    }

    // Battery & range

    public function testBatteryDataPopulation(): void
    {
        $data = new VehicleRealtimeData([
            'elecPercent' => '85.5',
            'powerBattery' => '72.3',
            'enduranceMileage' => '350',
            'evEndurance' => '320',
            'enduranceMileageV2' => '355.0',
            'enduranceMileageV2Unit' => 'km',
            'totalMileage' => '12000',
            'totalMileageV2' => '12500.5',
            'totalMileageV2Unit' => 'km',
        ]);

        $this->assertSame(85.5, $data->getElecPercent());
        $this->assertSame(72.3, $data->getPowerBattery());
        $this->assertSame(350.0, $data->getEnduranceMileage());
        $this->assertSame(320.0, $data->getEvEndurance());
        $this->assertSame(355.0, $data->getEnduranceMileageV2());
        $this->assertSame('km', $data->getEnduranceMileageV2Unit());
        $this->assertSame(12000.0, $data->getTotalMileage());
        $this->assertSame(12500.5, $data->getTotalMileageV2());
        $this->assertSame('km', $data->getTotalMileageV2Unit());
    }

    // Driving

    public function testPowerGearPopulation(): void
    {
        $data = new VehicleRealtimeData(['powerGear' => 3]);
        $this->assertSame(PowerGear::ON, $data->getPowerGear());
        $this->assertTrue($data->isVehicleOn());

        $data = new VehicleRealtimeData(['powerGear' => 1]);
        $this->assertSame(PowerGear::OFF, $data->getPowerGear());
        $this->assertFalse($data->isVehicleOn());
    }

    public function testSpeedPopulation(): void
    {
        $data = new VehicleRealtimeData(['speed' => '60.5']);
        $this->assertSame(60.5, $data->getSpeed());
    }

    // Climate & sentinel values

    public function testTempInCarNormalValue(): void
    {
        $data = new VehicleRealtimeData(['tempInCar' => '22.5']);
        $this->assertSame(22.5, $data->getTempInCar());
        $this->assertTrue($data->isInteriorTempAvailable());
    }

    public function testTempInCarSentinelMinus129(): void
    {
        $data = new VehicleRealtimeData(['tempInCar' => -129]);
        $this->assertNull($data->getTempInCar());
        $this->assertFalse($data->isInteriorTempAvailable());
    }

    public function testTempInCarSentinelMinus100(): void
    {
        // Exactly -100 should be treated as sentinel
        $data = new VehicleRealtimeData(['tempInCar' => -100]);
        $this->assertNull($data->getTempInCar());
    }

    public function testTempInCarNegativeButValid(): void
    {
        // -5°C is a valid temperature
        $data = new VehicleRealtimeData(['tempInCar' => -5]);
        $this->assertSame(-5.0, $data->getTempInCar());
        $this->assertTrue($data->isInteriorTempAvailable());
    }

    public function testAirRunStatePopulation(): void
    {
        $data = new VehicleRealtimeData(['airRunState' => 1]);
        $this->assertSame(AirCirculationMode::EXTERNAL, $data->getAirRunState());

        $data = new VehicleRealtimeData(['airRunState' => 2]);
        $this->assertSame(AirCirculationMode::INTERNAL, $data->getAirRunState());
    }

    // Seat heating/ventilation

    public function testSeatHeatStatesPopulation(): void
    {
        $data = new VehicleRealtimeData([
            'mainSeatHeatState' => 3,
            'mainSeatVentilationState' => 2,
            'copilotSeatHeatState' => 1,
            'copilotSeatVentilationState' => 0,
        ]);

        $this->assertSame(SeatHeatVentState::HIGH, $data->getMainSeatHeatState());
        $this->assertSame(SeatHeatVentState::LOW, $data->getMainSeatVentilationState());
        $this->assertSame(SeatHeatVentState::OFF, $data->getCopilotSeatHeatState());
        $this->assertSame(SeatHeatVentState::NO_DATA, $data->getCopilotSeatVentilationState());
    }

    public function testSteeringWheelHeatOn(): void
    {
        $data = new VehicleRealtimeData(['steeringWheelHeatState' => -1]);
        $this->assertSame(StearingWheelHeat::ON, $data->getSteeringWheelHeatState());
        $this->assertTrue($data->isSteeringWheelHeating());
    }

    public function testSteeringWheelHeatOff(): void
    {
        $data = new VehicleRealtimeData(['steeringWheelHeatState' => 1]);
        $this->assertSame(StearingWheelHeat::OFF, $data->getSteeringWheelHeatState());
        $this->assertFalse($data->isSteeringWheelHeating());
    }

    public function testStearingWheelHeatAliasPopulation(): void
    {
        // The old API field name stearingWheelHeatState (typo) should be aliased
        $data = new VehicleRealtimeData(['stearingWheelHeatState' => -1]);
        $this->assertSame(StearingWheelHeat::ON, $data->getSteeringWheelHeatState());
        $this->assertTrue($data->isSteeringWheelHeating());
    }

    // Charging

    public function testChargingStatePopulation(): void
    {
        $data = new VehicleRealtimeData(['chargingState' => 1]);
        $this->assertSame(ChargingState::CHARGING, $data->getChargingState());
        $this->assertTrue($data->isCharging());

        $data = new VehicleRealtimeData(['chargingState' => 0]);
        $this->assertSame(ChargingState::NOT_CHARGING, $data->getChargingState());
        $this->assertFalse($data->isCharging());

        $data = new VehicleRealtimeData(['chargingState' => 15]);
        $this->assertSame(ChargingState::NOT_CONNECTED, $data->getChargingState());
    }

    public function testFullHourMinuteSentinel(): void
    {
        // Negative hours/minutes should be normalized to null
        $data = new VehicleRealtimeData(['fullHour' => -1, 'fullMinute' => -1]);
        $this->assertNull($data->getFullHour());
        $this->assertNull($data->getFullMinute());
        $this->assertNull($data->getTimeToFullMinutes());
    }

    public function testFullHourMinutePositive(): void
    {
        $data = new VehicleRealtimeData(['fullHour' => 2, 'fullMinute' => 30]);
        $this->assertSame(2, $data->getFullHour());
        $this->assertSame(30, $data->getFullMinute());
        $this->assertSame(150, $data->getTimeToFullMinutes());
    }

    public function testRemainingHoursMinutesSentinel(): void
    {
        $data = new VehicleRealtimeData(['remainingHours' => -1, 'remainingMinutes' => -1]);
        $this->assertNull($data->getRemainingHours());
        $this->assertNull($data->getRemainingMinutes());
    }

    public function testRemainingHoursMinutesPositive(): void
    {
        $data = new VehicleRealtimeData(['remainingHours' => 1, 'remainingMinutes' => 15]);
        $this->assertSame(1, $data->getRemainingHours());
        $this->assertSame(15, $data->getRemainingMinutes());
    }

    // Doors

    public function testDoorStatePopulation(): void
    {
        $data = new VehicleRealtimeData([
            'leftFrontDoor' => 1,
            'rightFrontDoor' => 0,
            'leftRearDoor' => 1,
            'rightRearDoor' => 0,
            'trunkLid' => 0,
            'slidingDoor' => 0,
            'forehold' => 0,
        ]);

        $this->assertSame(DoorOpenState::OPEN, $data->getLeftFrontDoor());
        $this->assertSame(DoorOpenState::CLOSED, $data->getRightFrontDoor());
        $this->assertTrue($data->isAnyDoorOpen());
    }

    public function testDoorStateAliasBackCover(): void
    {
        $data = new VehicleRealtimeData(['backCover' => 1]);
        $this->assertSame(DoorOpenState::OPEN, $data->getTrunkLid());
        $this->assertTrue($data->isAnyDoorOpen());
    }

    public function testNoDoorOpen(): void
    {
        $data = new VehicleRealtimeData([
            'leftFrontDoor' => 0,
            'rightFrontDoor' => 0,
            'leftRearDoor' => 0,
            'rightRearDoor' => 0,
        ]);
        $this->assertFalse($data->isAnyDoorOpen());
    }

    // Locks

    public function testIsLockedAllLocked(): void
    {
        $data = new VehicleRealtimeData([
            'leftFrontDoorLock' => 2,
            'rightFrontDoorLock' => 2,
            'leftRearDoorLock' => 2,
            'rightRearDoorLock' => 2,
        ]);
        $this->assertTrue($data->isLocked());
    }

    public function testIsLockedOneUnlocked(): void
    {
        $data = new VehicleRealtimeData([
            'leftFrontDoorLock' => 2,
            'rightFrontDoorLock' => 1,
            'leftRearDoorLock' => 2,
            'rightRearDoorLock' => 2,
        ]);
        $this->assertFalse($data->isLocked());
    }

    public function testIsLockedAllUnknown(): void
    {
        $data = new VehicleRealtimeData([
            'leftFrontDoorLock' => -1,
            'rightFrontDoorLock' => -1,
            'leftRearDoorLock' => 0,
            'rightRearDoorLock' => 0,
        ]);
        $this->assertNull($data->isLocked());
    }

    public function testIsLockedMixKnownAndUnknown(): void
    {
        // Only known locks are considered; one lock is LOCKED, one is UNLOCKED
        $data = new VehicleRealtimeData([
            'leftFrontDoorLock' => 2,
            'rightFrontDoorLock' => 1,
            'leftRearDoorLock' => -1,
            'rightRearDoorLock' => 0,
        ]);
        $this->assertFalse($data->isLocked());
    }

    // Windows

    public function testWindowStatePopulation(): void
    {
        $data = new VehicleRealtimeData([
            'leftFrontWindow' => 2,
            'rightFrontWindow' => 1,
        ]);
        $this->assertSame(WindowState::OPEN, $data->getLeftFrontWindow());
        $this->assertSame(WindowState::CLOSED, $data->getRightFrontWindow());
        $this->assertTrue($data->isAnyWindowOpen());
    }

    public function testNoWindowOpen(): void
    {
        $data = new VehicleRealtimeData([
            'leftFrontWindow' => 1,
            'rightFrontWindow' => 1,
            'leftRearWindow' => 1,
            'rightRearWindow' => 1,
            'skylight' => 1,
        ]);
        $this->assertFalse($data->isAnyWindowOpen());
    }

    // Tire pressure

    public function testTirePressurePopulation(): void
    {
        $data = new VehicleRealtimeData([
            'leftFrontTirePressure' => '2.3',
            'rightFrontTirePressure' => '2.4',
            'leftRearTirePressure' => '2.2',
            'rightRearTirePressure' => '2.5',
            'tirePressUnit' => 1,
        ]);

        $this->assertSame(2.3, $data->getLeftFrontTirePressure());
        $this->assertSame(2.4, $data->getRightFrontTirePressure());
        $this->assertSame(2.2, $data->getLeftRearTirePressure());
        $this->assertSame(2.5, $data->getRightRearTirePressure());
        $this->assertSame(TirePressureUnit::BAR, $data->getTirePressUnit());
    }

    public function testTirePressureAliases(): void
    {
        $data = new VehicleRealtimeData([
            'leftFrontTirepressure' => '2.1',
            'rightFrontTirepressure' => '2.2',
            'leftRearTirepressure' => '2.3',
            'rightRearTirepressure' => '2.4',
        ]);

        $this->assertSame(2.1, $data->getLeftFrontTirePressure());
        $this->assertSame(2.2, $data->getRightFrontTirePressure());
        $this->assertSame(2.3, $data->getLeftRearTirePressure());
        $this->assertSame(2.4, $data->getRightRearTirePressure());
    }

    // Energy

    public function testEnergyDataPopulation(): void
    {
        $data = new VehicleRealtimeData([
            'totalPower' => '55.2',
            'gl' => '1.5',
            'totalEnergy' => '100',
            'nearestEnergyConsumption' => '15.2',
            'nearestEnergyConsumptionUnit' => 'kWh/100km',
            'recent50kmEnergy' => '12.5',
            'energyConsumption' => '14.8',
        ]);

        $this->assertSame(55.2, $data->getTotalPower());
        $this->assertSame(1.5, $data->getGl());
        $this->assertSame('100', $data->getTotalEnergy());
        $this->assertSame('15.2', $data->getNearestEnergyConsumption());
        $this->assertSame('kWh/100km', $data->getNearestEnergyConsumptionUnit());
        $this->assertSame('12.5', $data->getRecent50kmEnergy());
        $this->assertSame(14.8, $data->getEnergyConsumption());
    }

    // Oil sentinel values

    public function testOilEnduranceSentinel(): void
    {
        $data = new VehicleRealtimeData(['oilEndurance' => -1]);
        $this->assertNull($data->getOilEndurance());
    }

    public function testOilEnduranceNormal(): void
    {
        $data = new VehicleRealtimeData(['oilEndurance' => 450.0]);
        $this->assertSame(450.0, $data->getOilEndurance());
    }

    public function testOilPercentPopulation(): void
    {
        $data = new VehicleRealtimeData(['oilPercent' => -1]);
        $this->assertSame(-1.0, $data->getOilPercent());

        $data = new VehicleRealtimeData(['oilPercent' => 75.0]);
        $this->assertSame(75.0, $data->getOilPercent());

        $data = new VehicleRealtimeData([]);
        $this->assertNull($data->getOilPercent());
    }

    // EctValue sentinel

    public function testEctValueSentinel(): void
    {
        $data = new VehicleRealtimeData(['ectValue' => -1]);
        $this->assertNull($data->getEctValue());
    }

    public function testEctValueNormal(): void
    {
        $data = new VehicleRealtimeData(['ectValue' => 5]);
        $this->assertSame(5, $data->getEctValue());
    }

    // Battery heat state

    public function testBatteryHeatStateOn(): void
    {
        $data = new VehicleRealtimeData(['batteryHeatState' => 1]);
        $this->assertTrue($data->isBatteryHeating());
    }

    public function testBatteryHeatStateOff(): void
    {
        $data = new VehicleRealtimeData(['batteryHeatState' => 0]);
        $this->assertFalse($data->isBatteryHeating());
    }

    public function testBatteryHeatStateNull(): void
    {
        $data = new VehicleRealtimeData([]);
        $this->assertNull($data->isBatteryHeating());
    }

    // Timestamp

    public function testTimestampInSeconds(): void
    {
        $ts = 1700000000;
        $data = new VehicleRealtimeData(['time' => $ts]);
        $this->assertNotNull($data->getTimestamp());
        $this->assertSame($ts, $data->getTimestamp()->getTimestamp());
    }

    public function testTimestampInMilliseconds(): void
    {
        $tsMs = 1700000000000;
        $data = new VehicleRealtimeData(['time' => $tsMs]);
        $this->assertNotNull($data->getTimestamp());
        $this->assertSame(1700000000, $data->getTimestamp()->getTimestamp());
    }

    // isReadyRaw

    public function testIsReadyRawEmpty(): void
    {
        $this->assertFalse(VehicleRealtimeData::isReadyRaw([]));
    }

    public function testIsReadyRawOffline(): void
    {
        $this->assertFalse(VehicleRealtimeData::isReadyRaw(['onlineState' => 2]));
    }

    public function testIsReadyRawWithTirePressure(): void
    {
        $this->assertTrue(VehicleRealtimeData::isReadyRaw([
            'leftFrontTirepressure' => 2.3,
        ]));
    }

    public function testIsReadyRawWithTime(): void
    {
        $this->assertTrue(VehicleRealtimeData::isReadyRaw([
            'time' => 1700000000,
        ]));
    }

    public function testIsReadyRawWithEnduranceMileage(): void
    {
        $this->assertTrue(VehicleRealtimeData::isReadyRaw([
            'enduranceMileage' => 350,
        ]));
    }

    // requestSerial

    public function testRequestSerialPopulation(): void
    {
        $data = new VehicleRealtimeData(['requestSerial' => 'ABC123']);
        $this->assertSame('ABC123', $data->getRequestSerial());
    }

    // Full payload test

    public function testFullPayload(): void
    {
        $payload = [
            'onlineState' => 1,
            'connectState' => 1,
            'vehicleState' => 2,
            'elecPercent' => 80,
            'chargingState' => 0,
            'leftFrontDoor' => 0,
            'rightFrontDoor' => 0,
            'leftRearDoor' => 0,
            'rightRearDoor' => 0,
            'leftFrontDoorLock' => 2,
            'rightFrontDoorLock' => 2,
            'leftRearDoorLock' => 2,
            'rightRearDoorLock' => 2,
            'leftFrontWindow' => 1,
            'rightFrontWindow' => 1,
            'leftRearWindow' => 1,
            'rightRearWindow' => 1,
            'skylight' => 1,
            'tirePressUnit' => 1,
            'tempInCar' => 21.5,
            'powerGear' => 1,
        ];

        $data = new VehicleRealtimeData($payload);

        $this->assertTrue($data->isOnline());
        $this->assertSame(VehicleState::POWER_OFF, $data->getVehicleState());
        $this->assertSame(80.0, $data->getElecPercent());
        $this->assertFalse($data->isCharging());
        $this->assertFalse($data->isAnyDoorOpen());
        $this->assertTrue($data->isLocked());
        $this->assertFalse($data->isAnyWindowOpen());
        $this->assertSame(TirePressureUnit::BAR, $data->getTirePressUnit());
        $this->assertSame(21.5, $data->getTempInCar());
        $this->assertTrue($data->isInteriorTempAvailable());
        $this->assertFalse($data->isVehicleOn());
    }
}
