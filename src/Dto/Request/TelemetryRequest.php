<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Enum\EnergyType;
use Byd\ApiClient\Value\Vin;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class TelemetryRequest
{
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('energyType')]
        public EnergyType $energyType,
        #[SerializedName('tboxVersion')]
        public string $tboxVersion,
        #[SerializedName('requestSerial')]
        public ?string $requestSerial = null,
    ) {
    }
}
