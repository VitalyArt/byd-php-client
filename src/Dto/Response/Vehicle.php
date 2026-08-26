<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Byd\ApiClient\Value\Vin;

use function in_array;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class Vehicle
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('modelName')]
        public string $modelName = '',
        #[SerializedName('brandName')]
        public string $brandName = '',
        #[SerializedName('energyType')]
        public string $energyType = 'EV',
        #[SerializedName('autoAlias')]
        public string $alias = '',
        #[SerializedName('autoPlate')]
        public string $plate = '',
        #[SerializedName('picMainUrl')]
        public string $mainImageUrl = '',
        #[SerializedName('picSetUrl')]
        public string $imageSetUrl = '',
        #[SerializedName('outModelType')]
        public string $externalModelType = '',
        #[SerializedName('totalMileage')]
        public ?float $totalMileage = null,
        #[SerializedName('autoModelId')]
        public ?int $modelId = null,
        #[SerializedName('carType')]
        public ?int $carType = null,
        #[SerializedName('defaultCar')]
        public int|string|bool $defaultCar = false,
        #[SerializedName('tboxVersion')]
        public string $tboxVersion = '3',
        #[SerializedName('vehicleState')]
        public string $vehicleState = '',
        #[Ignore]
        public array $raw = [],
    ) {
    }

    public function isDefault(): bool
    {
        return in_array($this->defaultCar, [true, 1, '1'], true);
    }
}
