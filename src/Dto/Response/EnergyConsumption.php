<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class EnergyConsumption
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('cumulativeEnergyConsumption')]
        public ?CumulativeEnergyConsumption $cumulative = null,
        #[SerializedName('nearestEnergyConsumption')]
        public ?RecentEnergyConsumption $recent = null,
        #[SerializedName('selfGraph')]
        public ?EnergyConsumptionGraph $vehicleGraph = null,
        #[SerializedName('autoModelGraph')]
        public ?EnergyConsumptionGraph $modelGraph = null,
        #[SerializedName('totalMileage')]
        public ?float $totalMileage = null,
        #[SerializedName('totalEnergy')]
        public ?float $totalEnergy = null,
        #[SerializedName('recentAverageEnergy')]
        public ?float $recentAverage = null,
        #[SerializedName('recent50kmEnergy')]
        public float|string|null $recent50Km = null,
        #[SerializedName('drivingEnergy')]
        public ?float $drivingEnergy = null,
        #[SerializedName('chargingEnergy')]
        public ?float $chargingEnergy = null,
        #[SerializedName('electricMileage')]
        public ?float $electricMileage = null,
        #[SerializedName('fuelMileage')]
        public ?float $fuelMileage = null,
        #[SerializedName('co2Emission')]
        public ?float $co2Emission = null,
        #[SerializedName('co2Saved')]
        public ?float $co2Saved = null,
        #[SerializedName('mileageUnit')]
        public ?string $mileageUnit = null,
        #[SerializedName('cumulativeAverageEvConsumption')]
        public ?float $cumulativeAverageEvConsumption = null,
        #[SerializedName('cumulativeEvUnit')]
        public ?string $cumulativeEvUnit = null,
        #[SerializedName('timestamp')]
        public string|int|null $timestamp = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }
}
