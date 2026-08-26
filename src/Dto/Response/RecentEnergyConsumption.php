<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class RecentEnergyConsumption
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('avgEvConsumption')]
        public ?float $averageElectricConsumption = null,
        #[SerializedName('evConsumption')]
        public ?float $electricConsumption = null,
        #[SerializedName('evUnit')]
        public ?string $averageElectricConsumptionUnit = null,
        #[SerializedName('evValueUnit')]
        public ?string $electricConsumptionUnit = null,
        #[SerializedName('avgOilConsumption')]
        public float|string|null $averageFuelConsumption = null,
        #[SerializedName('oilConsumption')]
        public float|string|null $fuelConsumption = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }
}
