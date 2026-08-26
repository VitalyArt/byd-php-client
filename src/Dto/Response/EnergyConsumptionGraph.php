<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class EnergyConsumptionGraph
{
    /**
     * @param list<float|string> $values
     * @param array<string, mixed> $raw
     */
    public function __construct(
        #[SerializedName('energyConsumption')]
        public array $values = [],
        #[SerializedName('energyConsumptionUnit')]
        public ?string $unit = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }
}
