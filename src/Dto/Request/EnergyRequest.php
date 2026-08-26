<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Enum\PowerType;
use Byd\ApiClient\Value\Vin;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class EnergyRequest
{
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('powerType')]
        public PowerType $powerType,
        #[SerializedName('requestType')]
        public int $requestType = 0,
        #[SerializedName('autoModelNameOut')]
        public ?string $model = null,
    ) {
    }
}
