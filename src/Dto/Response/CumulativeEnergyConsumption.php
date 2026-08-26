<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class CumulativeEnergyConsumption
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('totalMileage')]
        public ?float $totalMileage = null,
        #[SerializedName('mileageUnit')]
        public ?string $mileageUnit = null,
        #[SerializedName('avgEvConsumption')]
        public ?float $averageElectricConsumption = null,
        #[SerializedName('evUnit')]
        public ?string $electricConsumptionUnit = null,
        #[SerializedName('avgOilConsumption')]
        public float|string|null $averageFuelConsumption = null,
        #[SerializedName('oilUnit')]
        public ?string $fuelConsumptionUnit = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }
}
