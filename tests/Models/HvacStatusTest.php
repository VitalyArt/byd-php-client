<?php

declare(strict_types=1);

namespace Byd\ApiClient\Tests\Models;

use Byd\ApiClient\Models\AcSwitch;
use Byd\ApiClient\Models\AirCirculationMode;
use Byd\ApiClient\Models\AirConditioningMode;
use Byd\ApiClient\Models\HvacOverallStatus;
use Byd\ApiClient\Models\HvacStatus;
use Byd\ApiClient\Models\HvacWindMode;
use Byd\ApiClient\Models\HvacWindPosition;
use Byd\ApiClient\Models\SeatHeatVentState;
use Byd\ApiClient\Models\StearingWheelHeat;
use PHPUnit\Framework\TestCase;

class HvacStatusTest extends TestCase
{
    // Default values when data is missing

    public function testDefaultsWhenEmpty(): void
    {
        $hvac = new HvacStatus([]);

        $this->assertSame(HvacOverallStatus::UNKNOWN, $hvac->getStatus());
        $this->assertSame(AcSwitch::UNKNOWN, $hvac->getAcSwitch());
        $this->assertSame(AirConditioningMode::UNKNOWN, $hvac->getAirConditioningMode());
        $this->assertSame(AirCirculationMode::UNKNOWN, $hvac->getCycleChoice());
        $this->assertSame(HvacWindMode::UNKNOWN, $hvac->getWindMode());
        $this->assertSame(HvacWindPosition::UNKNOWN, $hvac->getWindPosition());
        $this->assertNull($hvac->getTempInCar());
        $this->assertNull($hvac->getTempOutCar());
        $this->assertNull($hvac->getMainSettingTemp());
        $this->assertNull($hvac->getMainSettingTempNew());
        $this->assertNull($hvac->getCopilotSettingTemp());
        $this->assertNull($hvac->getCopilotSettingTempNew());
        $this->assertSame(SeatHeatVentState::UNKNOWN, $hvac->getMainSeatHeatState());
        $this->assertSame(StearingWheelHeat::OFF, $hvac->getSteeringWheelHeatState());
        $this->assertNull($hvac->getPm());
        $this->assertNull($hvac->getPm25StateOutCar());
        $this->assertFalse($hvac->isOn());
        $this->assertFalse($hvac->isAcActive());
    }

    // Status

    public function testStatusOn(): void
    {
        $hvac = new HvacStatus(['status' => 1]);
        $this->assertSame(HvacOverallStatus::ON, $hvac->getStatus());
        $this->assertTrue($hvac->isOn());
    }

    public function testStatusOff(): void
    {
        $hvac = new HvacStatus(['status' => 2]);
        $this->assertSame(HvacOverallStatus::OFF, $hvac->getStatus());
        $this->assertFalse($hvac->isOn());
    }

    public function testStatusUnknown(): void
    {
        $hvac = new HvacStatus(['status' => 99]);
        $this->assertSame(HvacOverallStatus::UNKNOWN, $hvac->getStatus());
    }

    // AcSwitch

    public function testAcSwitchOn(): void
    {
        $hvac = new HvacStatus(['acSwitch' => 1]);
        $this->assertSame(AcSwitch::ON, $hvac->getAcSwitch());
        $this->assertTrue($hvac->isAcActive());
    }

    public function testAcSwitchOff(): void
    {
        $hvac = new HvacStatus(['acSwitch' => 0]);
        $this->assertSame(AcSwitch::OFF, $hvac->getAcSwitch());
        $this->assertFalse($hvac->isAcActive());
    }

    public function testAcSwitchHeat(): void
    {
        $hvac = new HvacStatus(['acSwitch' => 2]);
        $this->assertSame(AcSwitch::HEAT, $hvac->getAcSwitch());
    }

    // AirConditioningMode

    public function testAirConditioningModePopulation(): void
    {
        $hvac = new HvacStatus(['airConditioningMode' => 1]);
        $this->assertSame(AirConditioningMode::AUTO, $hvac->getAirConditioningMode());

        $hvac = new HvacStatus(['airConditioningMode' => 2]);
        $this->assertSame(AirConditioningMode::MANUAL, $hvac->getAirConditioningMode());
    }

    // Wind mode

    public function testWindModePopulation(): void
    {
        $hvac = new HvacStatus(['windMode' => 1]);
        $this->assertSame(HvacWindMode::FACE, $hvac->getWindMode());

        $hvac = new HvacStatus(['windMode' => 5]);
        $this->assertSame(HvacWindMode::DEFROST, $hvac->getWindMode());
    }

    public function testWindPositionPopulation(): void
    {
        $hvac = new HvacStatus(['windPosition' => 3]);
        $this->assertSame(HvacWindPosition::POSITION_3, $hvac->getWindPosition());
    }

    // Cycle choice

    public function testCycleChoicePopulation(): void
    {
        $hvac = new HvacStatus(['cycleChoice' => 1]);
        $this->assertSame(AirCirculationMode::EXTERNAL, $hvac->getCycleChoice());

        $hvac = new HvacStatus(['cycleChoice' => 2]);
        $this->assertSame(AirCirculationMode::INTERNAL, $hvac->getCycleChoice());
    }

    // Temperatures

    public function testTempInCarNormal(): void
    {
        $hvac = new HvacStatus(['tempInCar' => 22.5]);
        $this->assertSame(22.5, $hvac->getTempInCar());
    }

    public function testTempInCarSentinelMinus129(): void
    {
        $hvac = new HvacStatus(['tempInCar' => -129]);
        $this->assertNull($hvac->getTempInCar());
    }

    public function testTempInCarSentinelBoundary(): void
    {
        $hvac = new HvacStatus(['tempInCar' => -100]);
        $this->assertNull($hvac->getTempInCar());
    }

    public function testTempInCarNegativeValid(): void
    {
        $hvac = new HvacStatus(['tempInCar' => -5.0]);
        $this->assertSame(-5.0, $hvac->getTempInCar());
    }

    public function testTempOutCar(): void
    {
        $hvac = new HvacStatus(['tempOutCar' => 15.0]);
        $this->assertSame(15.0, $hvac->getTempOutCar());
    }

    public function testSettingTemperatures(): void
    {
        $hvac = new HvacStatus([
            'mainSettingTemp' => 22,
            'mainSettingTempNew' => 22.5,
            'copilotSettingTemp' => 23,
            'copilotSettingTempNew' => 23.5,
        ]);

        $this->assertSame(22, $hvac->getMainSettingTemp());
        $this->assertSame(22.5, $hvac->getMainSettingTempNew());
        $this->assertSame(23, $hvac->getCopilotSettingTemp());
        $this->assertSame(23.5, $hvac->getCopilotSettingTempNew());
    }

    // Seat heating/ventilation

    public function testSeatStatesPopulation(): void
    {
        $hvac = new HvacStatus([
            'mainSeatHeatState' => 3,
            'mainSeatVentilationState' => 2,
            'copilotSeatHeatState' => 1,
            'copilotSeatVentilationState' => 0,
            'lrSeatHeatState' => 2,
            'lrSeatVentilationState' => 1,
            'rrSeatHeatState' => 1,
            'rrSeatVentilationState' => 0,
        ]);

        $this->assertSame(SeatHeatVentState::HIGH, $hvac->getMainSeatHeatState());
        $this->assertSame(SeatHeatVentState::LOW, $hvac->getMainSeatVentilationState());
        $this->assertSame(SeatHeatVentState::OFF, $hvac->getCopilotSeatHeatState());
        $this->assertSame(SeatHeatVentState::NO_DATA, $hvac->getCopilotSeatVentilationState());
        $this->assertSame(SeatHeatVentState::LOW, $hvac->getLrSeatHeatState());
        $this->assertSame(SeatHeatVentState::OFF, $hvac->getLrSeatVentilationState());
        $this->assertSame(SeatHeatVentState::OFF, $hvac->getRrSeatHeatState());
        $this->assertSame(SeatHeatVentState::NO_DATA, $hvac->getRrSeatVentilationState());
    }

    public function testSteeringWheelHeatOn(): void
    {
        $hvac = new HvacStatus(['steeringWheelHeatState' => -1]);
        $this->assertSame(StearingWheelHeat::ON, $hvac->getSteeringWheelHeatState());
    }

    public function testSteeringWheelHeatOff(): void
    {
        $hvac = new HvacStatus(['steeringWheelHeatState' => 1]);
        $this->assertSame(StearingWheelHeat::OFF, $hvac->getSteeringWheelHeatState());
    }

    // Air quality

    public function testPmPopulation(): void
    {
        $hvac = new HvacStatus(['pm' => 12.5, 'pm25StateOutCar' => 18.0]);
        $this->assertSame(12.5, $hvac->getPm());
        $this->assertSame(18.0, $hvac->getPm25StateOutCar());
    }

    // Misc fields

    public function testMiscFieldsPopulation(): void
    {
        $hvac = new HvacStatus([
            'frontDefrostStatus' => 1,
            'electricDefrostStatus' => 0,
            'wiperHeatStatus' => 1,
            'refrigeratorState' => 0,
            'refrigeratorDoorState' => 1,
            'rapidIncreaseTempState' => 0,
            'rapidDecreaseTempState' => 0,
            'whetherSupportAdjustTemp' => 1,
        ]);

        $this->assertSame(1, $hvac->getFrontDefrostStatus());
        $this->assertSame(0, $hvac->getElectricDefrostStatus());
        $this->assertSame(1, $hvac->getWiperHeatStatus());
        $this->assertSame(0, $hvac->getRefrigeratorState());
        $this->assertSame(1, $hvac->getRefrigeratorDoorState());
        $this->assertSame(0, $hvac->getRapidIncreaseTempState());
        $this->assertSame(0, $hvac->getRapidDecreaseTempState());
        $this->assertSame(1, $hvac->getWhetherSupportAdjustTemp());
    }

    // Full payload test

    public function testFullPayload(): void
    {
        $payload = [
            'status' => 1,
            'acSwitch' => 1,
            'airConditioningMode' => 1,
            'cycleChoice' => 1,
            'windMode' => 2,
            'windPosition' => 3,
            'tempInCar' => 22.0,
            'tempOutCar' => 10.0,
            'mainSettingTemp' => 22,
            'mainSettingTempNew' => 22.0,
            'mainSeatHeatState' => 3,
            'steeringWheelHeatState' => -1,
        ];

        $hvac = new HvacStatus($payload);

        $this->assertTrue($hvac->isOn());
        $this->assertTrue($hvac->isAcActive());
        $this->assertSame(AirConditioningMode::AUTO, $hvac->getAirConditioningMode());
        $this->assertSame(AirCirculationMode::EXTERNAL, $hvac->getCycleChoice());
        $this->assertSame(HvacWindMode::FACE_FOOT, $hvac->getWindMode());
        $this->assertSame(HvacWindPosition::POSITION_3, $hvac->getWindPosition());
        $this->assertSame(22.0, $hvac->getTempInCar());
        $this->assertSame(10.0, $hvac->getTempOutCar());
        $this->assertSame(22, $hvac->getMainSettingTemp());
        $this->assertSame(SeatHeatVentState::HIGH, $hvac->getMainSeatHeatState());
        $this->assertSame(StearingWheelHeat::ON, $hvac->getSteeringWheelHeatState());
    }
}
