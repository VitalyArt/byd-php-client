<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

use DateTimeImmutable;
use DateTimeInterface;

use function is_array;

/**
 * Energy consumption data.
 */
class EnergyConsumption extends BaseModel
{
    private ?float $totalMileage = null;

    private ?float $totalEnergy = null;

    private ?float $recentAverageEnergy = null;

    private ?float $recent50kmEnergy = null;

    private ?float $drivingEnergy = null;

    private ?float $chargingEnergy = null;

    private ?float $electricMileage = null;

    private ?float $fuelMileage = null;

    private ?float $totalMileageOfElectric = null;

    private ?float $totalMileageOfFuel = null;

    private ?float $totalEnergyOfElectric = null;

    private ?float $totalEnergyOfFuel = null;

    private ?float $co2Emission = null;

    private ?float $co2Saved = null;

    private ?DateTimeInterface $startTime = null;

    private ?DateTimeInterface $endTime = null;

    private ?string $mileageUnit = null;

    private ?float $cumulativeAverageEvConsumption = null;

    private ?string $cumulativeEvUnit = null;

    private ?float $last50kmAverageEvConsumption = null;

    private ?float $last50kmEvConsumption = null;

    private ?string $last50kmEvUnit = null;

    private ?string $last50kmEvValueUnit = null;

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        $cumulative = is_array($data['cumulativeEnergyConsumption'] ?? null) ? $data['cumulativeEnergyConsumption'] : [];
        $nearest = is_array($data['nearestEnergyConsumption'] ?? null) ? $data['nearestEnergyConsumption'] : [];

        $this->totalMileage = isset($cumulative['totalMileage']) ? (float) $cumulative['totalMileage'] : (isset($data['totalMileage']) ? (float) $data['totalMileage'] : null);
        $this->mileageUnit = isset($cumulative['mileageUnit']) ? self::normalizeUnit((string) $cumulative['mileageUnit']) : null;
        $this->cumulativeAverageEvConsumption = isset($cumulative['avgEvConsumption']) ? (float) $cumulative['avgEvConsumption'] : null;
        $this->cumulativeEvUnit = isset($cumulative['evUnit']) ? self::normalizeUnit((string) $cumulative['evUnit']) : null;
        $this->last50kmAverageEvConsumption = isset($nearest['avgEvConsumption']) ? (float) $nearest['avgEvConsumption'] : null;
        $this->last50kmEvConsumption = isset($nearest['evConsumption']) ? (float) $nearest['evConsumption'] : null;
        $this->last50kmEvUnit = isset($nearest['evUnit']) ? self::normalizeUnit((string) $nearest['evUnit']) : null;
        $this->last50kmEvValueUnit = isset($nearest['evValueUnit']) ? self::normalizeUnit((string) $nearest['evValueUnit']) : null;
        $this->totalEnergy = isset($data['totalEnergy']) ? (float) $data['totalEnergy'] : null;
        $this->recentAverageEnergy = $this->last50kmAverageEvConsumption ?? (isset($data['recentAverageEnergy']) ? (float) $data['recentAverageEnergy'] : null);
        $this->recent50kmEnergy = $this->last50kmEvConsumption ?? (isset($data['recent50kmEnergy']) ? (float) $data['recent50kmEnergy'] : null);
        $this->drivingEnergy = isset($data['drivingEnergy']) ? (float) $data['drivingEnergy'] : null;
        $this->chargingEnergy = isset($data['chargingEnergy']) ? (float) $data['chargingEnergy'] : null;
        $this->electricMileage = isset($data['electricMileage']) ? (float) $data['electricMileage'] : null;
        $this->fuelMileage = isset($data['fuelMileage']) ? (float) $data['fuelMileage'] : null;
        $this->totalMileageOfElectric = isset($data['totalMileageOfElectric']) ? (float) $data['totalMileageOfElectric'] : null;
        $this->totalMileageOfFuel = isset($data['totalMileageOfFuel']) ? (float) $data['totalMileageOfFuel'] : null;
        $this->totalEnergyOfElectric = isset($data['totalEnergyOfElectric']) ? (float) $data['totalEnergyOfElectric'] : null;
        $this->totalEnergyOfFuel = isset($data['totalEnergyOfFuel']) ? (float) $data['totalEnergyOfFuel'] : null;
        $this->co2Emission = isset($data['co2Emission']) ? (float) $data['co2Emission'] : null;
        $this->co2Saved = isset($data['co2Saved']) ? (float) $data['co2Saved'] : null;

        if (isset($data['startTime'])) {
            $this->startTime = $this->parseTimestamp($data['startTime']);
        }

        if (isset($data['endTime'])) {
            $this->endTime = $this->parseTimestamp($data['endTime']);
        }
    }

    private static function normalizeUnit(string $unit): string
    {
        return str_replace('·', '', $unit);
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
    public function getTotalMileage(): ?float
    {
        return $this->totalMileage;
    }

    public function getTotalEnergy(): ?float
    {
        return $this->totalEnergy;
    }

    public function getRecentAverageEnergy(): ?float
    {
        return $this->recentAverageEnergy;
    }

    public function getRecent50kmEnergy(): ?float
    {
        return $this->recent50kmEnergy;
    }

    public function getDrivingEnergy(): ?float
    {
        return $this->drivingEnergy;
    }

    public function getChargingEnergy(): ?float
    {
        return $this->chargingEnergy;
    }

    public function getElectricMileage(): ?float
    {
        return $this->electricMileage;
    }

    public function getFuelMileage(): ?float
    {
        return $this->fuelMileage;
    }

    public function getTotalMileageOfElectric(): ?float
    {
        return $this->totalMileageOfElectric;
    }

    public function getTotalMileageOfFuel(): ?float
    {
        return $this->totalMileageOfFuel;
    }

    public function getTotalEnergyOfElectric(): ?float
    {
        return $this->totalEnergyOfElectric;
    }

    public function getTotalEnergyOfFuel(): ?float
    {
        return $this->totalEnergyOfFuel;
    }

    public function getCo2Emission(): ?float
    {
        return $this->co2Emission;
    }

    public function getCo2Saved(): ?float
    {
        return $this->co2Saved;
    }

    public function getStartTime(): ?DateTimeInterface
    {
        return $this->startTime;
    }

    public function getEndTime(): ?DateTimeInterface
    {
        return $this->endTime;
    }

    public function getMileageUnit(): ?string
    {
        return $this->mileageUnit;
    }

    public function getCumulativeAverageEvConsumption(): ?float
    {
        return $this->cumulativeAverageEvConsumption;
    }

    public function getCumulativeEvUnit(): ?string
    {
        return $this->cumulativeEvUnit;
    }

    public function getLast50kmAverageEvConsumption(): ?float
    {
        return $this->last50kmAverageEvConsumption;
    }

    public function getLast50kmEvConsumption(): ?float
    {
        return $this->last50kmEvConsumption;
    }

    public function getLast50kmEvUnit(): ?string
    {
        return $this->last50kmEvUnit;
    }

    public function getLast50kmEvValueUnit(): ?string
    {
        return $this->last50kmEvValueUnit;
    }
}
