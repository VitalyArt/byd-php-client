<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * HVAC / climate status from the BYD API (/control/getStatusNow).
 */
class HvacStatus extends BaseModel
{
    private HvacOverallStatus $status = HvacOverallStatus::UNKNOWN;

    private AcSwitch $acSwitch = AcSwitch::UNKNOWN;

    private AirConditioningMode $airConditioningMode = AirConditioningMode::UNKNOWN;

    private AirCirculationMode $cycleChoice = AirCirculationMode::UNKNOWN;

    private HvacWindMode $windMode = HvacWindMode::UNKNOWN;

    private HvacWindPosition $windPosition = HvacWindPosition::UNKNOWN;

    // Temperatures
    private ?float $tempInCar = null;

    private ?float $tempOutCar = null;

    private ?int $mainSettingTemp = null;

    private ?float $mainSettingTempNew = null;

    private ?int $copilotSettingTemp = null;

    private ?float $copilotSettingTempNew = null;

    // Seat heating/ventilation
    private SeatHeatVentState $mainSeatHeatState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $mainSeatVentilationState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $copilotSeatHeatState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $copilotSeatVentilationState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $lrSeatHeatState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $lrSeatVentilationState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $rrSeatHeatState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $rrSeatVentilationState = SeatHeatVentState::UNKNOWN;

    private StearingWheelHeat $steeringWheelHeatState = StearingWheelHeat::OFF;

    // Air quality
    private ?float $pm = null;

    private ?float $pm25StateOutCar = null;

    // Misc
    private ?int $frontDefrostStatus = null;

    private ?int $electricDefrostStatus = null;

    private ?int $wiperHeatStatus = null;

    private ?int $refrigeratorState = null;

    private ?int $refrigeratorDoorState = null;

    private ?int $rapidIncreaseTempState = null;

    private ?int $rapidDecreaseTempState = null;

    private ?int $whetherSupportAdjustTemp = null;

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->status = HvacOverallStatus::tryFrom((int) ($data['status'] ?? -1)) ?? HvacOverallStatus::UNKNOWN;
        $this->acSwitch = AcSwitch::tryFrom((int) ($data['acSwitch'] ?? -1)) ?? AcSwitch::UNKNOWN;
        $this->airConditioningMode = AirConditioningMode::tryFrom((int) ($data['airConditioningMode'] ?? -1)) ?? AirConditioningMode::UNKNOWN;
        $this->cycleChoice = AirCirculationMode::tryFrom((int) ($data['cycleChoice'] ?? -1)) ?? AirCirculationMode::UNKNOWN;
        $this->windMode = HvacWindMode::tryFrom((int) ($data['windMode'] ?? -1)) ?? HvacWindMode::UNKNOWN;
        $this->windPosition = HvacWindPosition::tryFrom((int) ($data['windPosition'] ?? -1)) ?? HvacWindPosition::UNKNOWN;

        // Temperatures — tempInCar uses -129 as "no data" sentinel
        $rawTempInCar = isset($data['tempInCar']) ? (float) $data['tempInCar'] : null;
        $this->tempInCar = ($rawTempInCar !== null && $rawTempInCar <= -100.0) ? null : $rawTempInCar;

        $this->tempOutCar = isset($data['tempOutCar']) ? (float) $data['tempOutCar'] : null;
        $this->mainSettingTemp = isset($data['mainSettingTemp']) ? (int) $data['mainSettingTemp'] : null;
        $this->mainSettingTempNew = isset($data['mainSettingTempNew']) ? (float) $data['mainSettingTempNew'] : null;
        $this->copilotSettingTemp = isset($data['copilotSettingTemp']) ? (int) $data['copilotSettingTemp'] : null;
        $this->copilotSettingTempNew = isset($data['copilotSettingTempNew']) ? (float) $data['copilotSettingTempNew'] : null;

        // Seat heating/ventilation
        $this->mainSeatHeatState = SeatHeatVentState::tryFrom((int) ($data['mainSeatHeatState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->mainSeatVentilationState = SeatHeatVentState::tryFrom((int) ($data['mainSeatVentilationState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->copilotSeatHeatState = SeatHeatVentState::tryFrom((int) ($data['copilotSeatHeatState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->copilotSeatVentilationState = SeatHeatVentState::tryFrom((int) ($data['copilotSeatVentilationState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->lrSeatHeatState = SeatHeatVentState::tryFrom((int) ($data['lrSeatHeatState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->lrSeatVentilationState = SeatHeatVentState::tryFrom((int) ($data['lrSeatVentilationState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->rrSeatHeatState = SeatHeatVentState::tryFrom((int) ($data['rrSeatHeatState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->rrSeatVentilationState = SeatHeatVentState::tryFrom((int) ($data['rrSeatVentilationState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->steeringWheelHeatState = StearingWheelHeat::tryFrom((int) ($data['steeringWheelHeatState'] ?? 1)) ?? StearingWheelHeat::OFF;

        // Air quality
        $this->pm = isset($data['pm']) ? (float) $data['pm'] : null;
        $this->pm25StateOutCar = isset($data['pm25StateOutCar']) ? (float) $data['pm25StateOutCar'] : null;

        // Misc
        $this->frontDefrostStatus = isset($data['frontDefrostStatus']) ? (int) $data['frontDefrostStatus'] : null;
        $this->electricDefrostStatus = isset($data['electricDefrostStatus']) ? (int) $data['electricDefrostStatus'] : null;
        $this->wiperHeatStatus = isset($data['wiperHeatStatus']) ? (int) $data['wiperHeatStatus'] : null;
        $this->refrigeratorState = isset($data['refrigeratorState']) ? (int) $data['refrigeratorState'] : null;
        $this->refrigeratorDoorState = isset($data['refrigeratorDoorState']) ? (int) $data['refrigeratorDoorState'] : null;
        $this->rapidIncreaseTempState = isset($data['rapidIncreaseTempState']) ? (int) $data['rapidIncreaseTempState'] : null;
        $this->rapidDecreaseTempState = isset($data['rapidDecreaseTempState']) ? (int) $data['rapidDecreaseTempState'] : null;
        $this->whetherSupportAdjustTemp = isset($data['whetherSupportAdjustTemp']) ? (int) $data['whetherSupportAdjustTemp'] : null;
    }

    // Getters
    public function getStatus(): HvacOverallStatus
    {
        return $this->status;
    }

    public function getAcSwitch(): AcSwitch
    {
        return $this->acSwitch;
    }

    public function getAirConditioningMode(): AirConditioningMode
    {
        return $this->airConditioningMode;
    }

    public function getCycleChoice(): AirCirculationMode
    {
        return $this->cycleChoice;
    }

    public function getWindMode(): HvacWindMode
    {
        return $this->windMode;
    }

    public function getWindPosition(): HvacWindPosition
    {
        return $this->windPosition;
    }

    public function getTempInCar(): ?float
    {
        return $this->tempInCar;
    }

    public function getTempOutCar(): ?float
    {
        return $this->tempOutCar;
    }

    public function getMainSettingTemp(): ?int
    {
        return $this->mainSettingTemp;
    }

    public function getMainSettingTempNew(): ?float
    {
        return $this->mainSettingTempNew;
    }

    public function getCopilotSettingTemp(): ?int
    {
        return $this->copilotSettingTemp;
    }

    public function getCopilotSettingTempNew(): ?float
    {
        return $this->copilotSettingTempNew;
    }

    public function getMainSeatHeatState(): SeatHeatVentState
    {
        return $this->mainSeatHeatState;
    }

    public function getMainSeatVentilationState(): SeatHeatVentState
    {
        return $this->mainSeatVentilationState;
    }

    public function getCopilotSeatHeatState(): SeatHeatVentState
    {
        return $this->copilotSeatHeatState;
    }

    public function getCopilotSeatVentilationState(): SeatHeatVentState
    {
        return $this->copilotSeatVentilationState;
    }

    public function getLrSeatHeatState(): SeatHeatVentState
    {
        return $this->lrSeatHeatState;
    }

    public function getLrSeatVentilationState(): SeatHeatVentState
    {
        return $this->lrSeatVentilationState;
    }

    public function getRrSeatHeatState(): SeatHeatVentState
    {
        return $this->rrSeatHeatState;
    }

    public function getRrSeatVentilationState(): SeatHeatVentState
    {
        return $this->rrSeatVentilationState;
    }

    public function getSteeringWheelHeatState(): StearingWheelHeat
    {
        return $this->steeringWheelHeatState;
    }

    public function getPm(): ?float
    {
        return $this->pm;
    }

    public function getPm25StateOutCar(): ?float
    {
        return $this->pm25StateOutCar;
    }

    public function getFrontDefrostStatus(): ?int
    {
        return $this->frontDefrostStatus;
    }

    public function getElectricDefrostStatus(): ?int
    {
        return $this->electricDefrostStatus;
    }

    public function getWiperHeatStatus(): ?int
    {
        return $this->wiperHeatStatus;
    }

    public function getRefrigeratorState(): ?int
    {
        return $this->refrigeratorState;
    }

    public function getRefrigeratorDoorState(): ?int
    {
        return $this->refrigeratorDoorState;
    }

    public function getRapidIncreaseTempState(): ?int
    {
        return $this->rapidIncreaseTempState;
    }

    public function getRapidDecreaseTempState(): ?int
    {
        return $this->rapidDecreaseTempState;
    }

    public function getWhetherSupportAdjustTemp(): ?int
    {
        return $this->whetherSupportAdjustTemp;
    }

    public function isOn(): bool
    {
        return $this->status === HvacOverallStatus::ON;
    }

    public function isAcActive(): bool
    {
        return $this->acSwitch === AcSwitch::ON;
    }
}
