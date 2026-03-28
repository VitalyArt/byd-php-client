<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

use DateTimeImmutable;
use DateTimeInterface;

/**
 * Charging status.
 */
class ChargingStatus extends BaseModel
{
    private ChargingState $chargingState = ChargingState::UNKNOWN;

    private ?float $chargerPower = null;

    private ?float $chargerVoltage = null;

    private ?float $chargerCurrent = null;

    private ?float $batteryCapacity = null;

    private ?float $batteryVoltage = null;

    private ?float $batteryCurrent = null;

    private ?float $batteryTemperature = null;

    private ?float $batterySOC = null;

    private ?float $chargingPower = null;

    private ?int $chargingTime = null;

    private ?int $remainingTime = null;

    private ?float $mileageOfCharge = null;

    private ?float $mileageOfDay = null;

    private ?float $mileageOfWeek = null;

    private ?float $mileageOfMonth = null;

    private ?DateTimeInterface $startTime = null;

    private ?DateTimeInterface $endTime = null;

    private ?string $chargingPileName = null;

    private ?string $chargingPileSN = null;

    private ?int $chargingType = null;

    private ?float $chargingCost = null;

    private ?float $electricPrice = null;

    private ?float $serviceFee = null;

    private ?float $totalFee = null;

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $this->chargingState = ChargingState::tryFrom((int) ($data['chargingState'] ?? -1)) ?? ChargingState::UNKNOWN;
        $this->chargerPower = isset($data['chargerPower']) ? (float) $data['chargerPower'] : null;
        $this->chargerVoltage = isset($data['chargerVoltage']) ? (float) $data['chargerVoltage'] : null;
        $this->chargerCurrent = isset($data['chargerCurrent']) ? (float) $data['chargerCurrent'] : null;
        $this->batteryCapacity = isset($data['batteryCapacity']) ? (float) $data['batteryCapacity'] : null;
        $this->batteryVoltage = isset($data['batteryVoltage']) ? (float) $data['batteryVoltage'] : null;
        $this->batteryCurrent = isset($data['batteryCurrent']) ? (float) $data['batteryCurrent'] : null;
        $this->batteryTemperature = isset($data['batteryTemperature']) ? (float) $data['batteryTemperature'] : null;
        $this->batterySOC = isset($data['batterySOC']) ? (float) $data['batterySOC'] : null;
        $this->chargingPower = isset($data['chargingPower']) ? (float) $data['chargingPower'] : null;
        $this->chargingTime = isset($data['chargingTime']) ? (int) $data['chargingTime'] : null;
        $this->remainingTime = isset($data['remainingTime']) ? (int) $data['remainingTime'] : null;
        $this->mileageOfCharge = isset($data['mileageOfCharge']) ? (float) $data['mileageOfCharge'] : null;
        $this->mileageOfDay = isset($data['mileageOfDay']) ? (float) $data['mileageOfDay'] : null;
        $this->mileageOfWeek = isset($data['mileageOfWeek']) ? (float) $data['mileageOfWeek'] : null;
        $this->mileageOfMonth = isset($data['mileageOfMonth']) ? (float) $data['mileageOfMonth'] : null;

        if (isset($data['startTime'])) {
            $this->startTime = $this->parseTimestamp($data['startTime']);
        }

        if (isset($data['endTime'])) {
            $this->endTime = $this->parseTimestamp($data['endTime']);
        }

        $this->chargingPileName = isset($data['chargingPileName']) ? (string) $data['chargingPileName'] : null;
        $this->chargingPileSN = isset($data['chargingPileSN']) ? (string) $data['chargingPileSN'] : null;
        $this->chargingType = isset($data['chargingType']) ? (int) $data['chargingType'] : null;
        $this->chargingCost = isset($data['chargingCost']) ? (float) $data['chargingCost'] : null;
        $this->electricPrice = isset($data['electricPrice']) ? (float) $data['electricPrice'] : null;
        $this->serviceFee = isset($data['serviceFee']) ? (float) $data['serviceFee'] : null;
        $this->totalFee = isset($data['totalFee']) ? (float) $data['totalFee'] : null;
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

    // Getters
    public function getChargingState(): ChargingState
    {
        return $this->chargingState;
    }

    public function getChargerPower(): ?float
    {
        return $this->chargerPower;
    }

    public function getChargerVoltage(): ?float
    {
        return $this->chargerVoltage;
    }

    public function getChargerCurrent(): ?float
    {
        return $this->chargerCurrent;
    }

    public function getBatteryCapacity(): ?float
    {
        return $this->batteryCapacity;
    }

    public function getBatteryVoltage(): ?float
    {
        return $this->batteryVoltage;
    }

    public function getBatteryCurrent(): ?float
    {
        return $this->batteryCurrent;
    }

    public function getBatteryTemperature(): ?float
    {
        return $this->batteryTemperature;
    }

    public function getBatterySOC(): ?float
    {
        return $this->batterySOC;
    }

    public function getChargingPower(): ?float
    {
        return $this->chargingPower;
    }

    public function getChargingTime(): ?int
    {
        return $this->chargingTime;
    }

    public function getRemainingTime(): ?int
    {
        return $this->remainingTime;
    }

    public function getMileageOfCharge(): ?float
    {
        return $this->mileageOfCharge;
    }

    public function getMileageOfDay(): ?float
    {
        return $this->mileageOfDay;
    }

    public function getMileageOfWeek(): ?float
    {
        return $this->mileageOfWeek;
    }

    public function getMileageOfMonth(): ?float
    {
        return $this->mileageOfMonth;
    }

    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }

    public function getEndTime(): ?DateTimeInterface
    {
        return $this->endTime;
    }

    public function getChargingPileName(): ?string
    {
        return $this->chargingPileName;
    }

    public function getChargingPileSN(): ?string
    {
        return $this->chargingPileSN;
    }

    public function getChargingType(): ?int
    {
        return $this->chargingType;
    }

    public function getChargingCost(): ?float
    {
        return $this->chargingCost;
    }

    public function getElectricPrice(): ?float
    {
        return $this->electricPrice;
    }

    public function getServiceFee(): ?float
    {
        return $this->serviceFee;
    }

    public function getTotalFee(): ?float
    {
        return $this->totalFee;
    }
}
