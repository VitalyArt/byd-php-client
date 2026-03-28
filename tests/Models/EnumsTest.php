<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests\Models;

use Byd\ApiClient\Models\AcSwitch;
use Byd\ApiClient\Models\AirCirculationMode;
use Byd\ApiClient\Models\AirConditioningMode;
use Byd\ApiClient\Models\ChargingState;
use Byd\ApiClient\Models\ConnectState;
use Byd\ApiClient\Models\DoorOpenState;
use Byd\ApiClient\Models\HvacOverallStatus;
use Byd\ApiClient\Models\HvacWindMode;
use Byd\ApiClient\Models\HvacWindPosition;
use Byd\ApiClient\Models\LockState;
use Byd\ApiClient\Models\OnlineState;
use Byd\ApiClient\Models\PowerGear;
use Byd\ApiClient\Models\SeatHeatVentState;
use Byd\ApiClient\Models\StearingWheelHeat;
use Byd\ApiClient\Models\TirePressureUnit;
use Byd\ApiClient\Models\VehicleState;
use Byd\ApiClient\Models\WindowState;
use PHPUnit\Framework\TestCase;

class EnumsTest extends TestCase
{
    // OnlineState

    public function testOnlineStateValues(): void
    {
        $this->assertSame(-1, OnlineState::UNKNOWN->value);
        $this->assertSame(1, OnlineState::ONLINE->value);
        $this->assertSame(2, OnlineState::OFFLINE->value);
    }

    public function testOnlineStateTryFrom(): void
    {
        $this->assertSame(OnlineState::ONLINE, OnlineState::tryFrom(1));
        $this->assertSame(OnlineState::OFFLINE, OnlineState::tryFrom(2));
        $this->assertSame(OnlineState::UNKNOWN, OnlineState::tryFrom(-1));
        $this->assertNull(OnlineState::tryFrom(99));
    }

    // ChargingState

    public function testChargingStateValues(): void
    {
        $this->assertSame(-1, ChargingState::UNKNOWN->value);
        $this->assertSame(0, ChargingState::NOT_CHARGING->value);
        $this->assertSame(1, ChargingState::CHARGING->value);
        $this->assertSame(15, ChargingState::CONNECTED->value);
    }

    public function testChargingStateTryFrom(): void
    {
        $this->assertSame(ChargingState::CHARGING, ChargingState::tryFrom(1));
        $this->assertSame(ChargingState::NOT_CHARGING, ChargingState::tryFrom(0));
        $this->assertSame(ChargingState::CONNECTED, ChargingState::tryFrom(15));
        $this->assertNull(ChargingState::tryFrom(99));
    }

    // LockState

    public function testLockStateValues(): void
    {
        $this->assertSame(-1, LockState::UNKNOWN->value);
        $this->assertSame(0, LockState::UNAVAILABLE->value);
        $this->assertSame(1, LockState::UNLOCKED->value);
        $this->assertSame(2, LockState::LOCKED->value);
    }

    public function testLockStateTryFrom(): void
    {
        $this->assertSame(LockState::LOCKED, LockState::tryFrom(2));
        $this->assertSame(LockState::UNLOCKED, LockState::tryFrom(1));
        $this->assertNull(LockState::tryFrom(99));
    }

    // DoorOpenState

    public function testDoorOpenStateValues(): void
    {
        $this->assertSame(-1, DoorOpenState::UNKNOWN->value);
        $this->assertSame(0, DoorOpenState::CLOSED->value);
        $this->assertSame(1, DoorOpenState::OPEN->value);
    }

    public function testDoorOpenStateTryFrom(): void
    {
        $this->assertSame(DoorOpenState::OPEN, DoorOpenState::tryFrom(1));
        $this->assertSame(DoorOpenState::CLOSED, DoorOpenState::tryFrom(0));
        $this->assertNull(DoorOpenState::tryFrom(99));
    }

    // WindowState

    public function testWindowStateValues(): void
    {
        $this->assertSame(-1, WindowState::UNKNOWN->value);
        $this->assertSame(1, WindowState::CLOSED->value);
        $this->assertSame(2, WindowState::OPEN->value);
    }

    public function testWindowStateTryFrom(): void
    {
        $this->assertSame(WindowState::CLOSED, WindowState::tryFrom(1));
        $this->assertSame(WindowState::OPEN, WindowState::tryFrom(2));
        $this->assertNull(WindowState::tryFrom(99));
    }

    // AirCirculationMode

    public function testAirCirculationModeValues(): void
    {
        $this->assertSame(-1, AirCirculationMode::UNKNOWN->value);
        $this->assertSame(0, AirCirculationMode::UNAVAILABLE->value);
        $this->assertSame(1, AirCirculationMode::EXTERNAL->value);
        $this->assertSame(2, AirCirculationMode::INTERNAL->value);
    }

    // SeatHeatVentState

    public function testSeatHeatVentStateValues(): void
    {
        $this->assertSame(-1, SeatHeatVentState::UNKNOWN->value);
        $this->assertSame(0, SeatHeatVentState::NO_DATA->value);
        $this->assertSame(1, SeatHeatVentState::OFF->value);
        $this->assertSame(2, SeatHeatVentState::LOW->value);
        $this->assertSame(3, SeatHeatVentState::HIGH->value);
    }

    public function testSeatHeatVentStateToCommandLevel(): void
    {
        $this->assertSame(1, SeatHeatVentState::HIGH->toCommandLevel());
        $this->assertSame(2, SeatHeatVentState::LOW->toCommandLevel());
        $this->assertSame(3, SeatHeatVentState::OFF->toCommandLevel());
        $this->assertSame(0, SeatHeatVentState::NO_DATA->toCommandLevel());
        $this->assertSame(0, SeatHeatVentState::UNKNOWN->toCommandLevel());
    }

    // StearingWheelHeat

    public function testStearingWheelHeatValues(): void
    {
        $this->assertSame(-1, StearingWheelHeat::ON->value);
        $this->assertSame(1, StearingWheelHeat::OFF->value);
    }

    public function testStearingWheelHeatToCommandLevel(): void
    {
        $this->assertSame(1, StearingWheelHeat::ON->toCommandLevel());
        $this->assertSame(3, StearingWheelHeat::OFF->toCommandLevel());
    }

    public function testStearingWheelHeatTryFrom(): void
    {
        $this->assertSame(StearingWheelHeat::ON, StearingWheelHeat::tryFrom(-1));
        $this->assertSame(StearingWheelHeat::OFF, StearingWheelHeat::tryFrom(1));
        $this->assertNull(StearingWheelHeat::tryFrom(0));
    }

    // ConnectState

    public function testConnectStateValues(): void
    {
        $this->assertSame(-1, ConnectState::UNKNOWN->value);
        $this->assertSame(0, ConnectState::DISCONNECTED->value);
        $this->assertSame(1, ConnectState::CONNECTED->value);
    }

    // PowerGear

    public function testPowerGearValues(): void
    {
        $this->assertSame(-1, PowerGear::UNKNOWN->value);
        $this->assertSame(1, PowerGear::OFF->value);
        $this->assertSame(3, PowerGear::ON->value);
    }

    public function testPowerGearTryFrom(): void
    {
        $this->assertSame(PowerGear::ON, PowerGear::tryFrom(3));
        $this->assertSame(PowerGear::OFF, PowerGear::tryFrom(1));
        $this->assertSame(PowerGear::UNKNOWN, PowerGear::tryFrom(-1));
        $this->assertNull(PowerGear::tryFrom(2));
    }

    // VehicleState

    public function testVehicleStateValues(): void
    {
        $this->assertSame(-1, VehicleState::UNKNOWN->value);
        $this->assertSame(0, VehicleState::OFF->value);
        $this->assertSame(2, VehicleState::ON->value);
    }

    // TirePressureUnit

    public function testTirePressureUnitValues(): void
    {
        $this->assertSame(-1, TirePressureUnit::UNKNOWN->value);
        $this->assertSame(1, TirePressureUnit::BAR->value);
        $this->assertSame(2, TirePressureUnit::PSI->value);
        $this->assertSame(3, TirePressureUnit::KPA->value);
    }

    public function testTirePressureUnitNames(): void
    {
        $this->assertSame('BAR', TirePressureUnit::BAR->name);
        $this->assertSame('PSI', TirePressureUnit::PSI->name);
        $this->assertSame('KPA', TirePressureUnit::KPA->name);
    }

    // AcSwitch

    public function testAcSwitchValues(): void
    {
        $this->assertSame(-1, AcSwitch::UNKNOWN->value);
        $this->assertSame(0, AcSwitch::OFF->value);
        $this->assertSame(1, AcSwitch::ON->value);
    }

    // AirConditioningMode

    public function testAirConditioningModeValues(): void
    {
        $this->assertSame(-1, AirConditioningMode::UNKNOWN->value);
        $this->assertSame(0, AirConditioningMode::OFF->value);
        $this->assertSame(1, AirConditioningMode::AUTO->value);
        $this->assertSame(2, AirConditioningMode::MANUAL->value);
    }

    // HvacOverallStatus

    public function testHvacOverallStatusValues(): void
    {
        $this->assertSame(-1, HvacOverallStatus::UNKNOWN->value);
        $this->assertSame(1, HvacOverallStatus::ON->value);
        $this->assertSame(2, HvacOverallStatus::OFF->value);
    }

    // HvacWindMode

    public function testHvacWindModeValues(): void
    {
        $this->assertSame(-1, HvacWindMode::UNKNOWN->value);
        $this->assertSame(0, HvacWindMode::OFF->value);
        $this->assertSame(1, HvacWindMode::FACE->value);
        $this->assertSame(2, HvacWindMode::FACE_FOOT->value);
        $this->assertSame(3, HvacWindMode::FOOT->value);
        $this->assertSame(4, HvacWindMode::FOOT_DEFROST->value);
        $this->assertSame(5, HvacWindMode::DEFROST->value);
    }

    // HvacWindPosition

    public function testHvacWindPositionValues(): void
    {
        $this->assertSame(-1, HvacWindPosition::UNKNOWN->value);
        $this->assertSame(0, HvacWindPosition::OFF->value);
        $this->assertSame(1, HvacWindPosition::POSITION_1->value);
        $this->assertSame(7, HvacWindPosition::POSITION_7->value);
    }

    public function testHvacWindPositionTryFrom(): void
    {
        $this->assertSame(HvacWindPosition::POSITION_3, HvacWindPosition::tryFrom(3));
        $this->assertNull(HvacWindPosition::tryFrom(99));
    }
}
