<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

/**
 * HVAC / climate status.
 */
class HvacStatus extends BaseModel
{
    private ?float $interiorTemperature = null;
    private ?int $driverTemperature = null;
    private ?float $driverTemperatureCelsius = null;
    private ?int $passengerTemperature = null;
    private ?float $passengerTemperatureCelsius = null;
    private int $acRunning = -1;
    private int $acMode = -1;
    private int $circulationMode = AirCirculationMode::UNKNOWN;
    private int $driverSeatHeat = SeatHeatVentState::UNKNOWN;
    private int $driverSeatVent = SeatHeatVentState::UNKNOWN;
    private int $passengerSeatHeat = SeatHeatVentState::UNKNOWN;
    private int $passengerSeatVent = SeatHeatVentState::UNKNOWN;
    private int $steeringWheelHeat = StearingWheelHeat::OFF;

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->interiorTemperature = isset($data['interiorTemperature']) ? (float) $data['interiorTemperature'] : null;
        $this->driverTemperature = isset($data['driverTemperature']) ? (int) $data['driverTemperature'] : null;
        $this->driverTemperatureCelsius = isset($data['driverTemperatureCelsius']) ? (float) $data['driverTemperatureCelsius'] : null;
        $this->passengerTemperature = isset($data['passengerTemperature']) ? (int) $data['passengerTemperature'] : null;
        $this->passengerTemperatureCelsius = isset($data['passengerTemperatureCelsius']) ? (float) $data['passengerTemperatureCelsius'] : null;
        $this->acRunning = (int) ($data['acRunning'] ?? -1);
        $this->acMode = (int) ($data['acMode'] ?? -1);
        $this->circulationMode = (int) ($data['circulationMode'] ?? AirCirculationMode::UNKNOWN);
        $this->driverSeatHeat = (int) ($data['driverSeatHeat'] ?? SeatHeatVentState::UNKNOWN);
        $this->driverSeatVent = (int) ($data['driverSeatVent'] ?? SeatHeatVentState::UNKNOWN);
        $this->passengerSeatHeat = (int) ($data['passengerSeatHeat'] ?? SeatHeatVentState::UNKNOWN);
        $this->passengerSeatVent = (int) ($data['passengerSeatVent'] ?? SeatHeatVentState::UNKNOWN);
        $this->steeringWheelHeat = (int) ($data['steeringWheelHeat'] ?? StearingWheelHeat::OFF);
    }

    // Getters
    public function getInteriorTemperature(): ?float
    {
        return $this->interiorTemperature;
    }

    public function getDriverTemperature(): ?int
    {
        return $this->driverTemperature;
    }

    public function getDriverTemperatureCelsius(): ?float
    {
        return $this->driverTemperatureCelsius;
    }

    public function getPassengerTemperature(): ?int
    {
        return $this->passengerTemperature;
    }

    public function getPassengerTemperatureCelsius(): ?float
    {
        return $this->passengerTemperatureCelsius;
    }

    public function getAcRunning(): int
    {
        return $this->acRunning;
    }

    public function getAcMode(): int
    {
        return $this->acMode;
    }

    public function getCirculationMode(): int
    {
        return $this->circulationMode;
    }

    public function getDriverSeatHeat(): int
    {
        return $this->driverSeatHeat;
    }

    public function getDriverSeatVent(): int
    {
        return $this->driverSeatVent;
    }

    public function getPassengerSeatHeat(): int
    {
        return $this->passengerSeatHeat;
    }

    public function getPassengerSeatVent(): int
    {
        return $this->passengerSeatVent;
    }

    public function getSteeringWheelHeat(): int
    {
        return $this->steeringWheelHeat;
    }
}
