<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Exception\ValidationException;
use Byd\ApiClient\Value\Vin;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class RenameVehicleRequest
{
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('autoAlias')]
        public string $name,
    ) {
        if (trim($name) === '' || mb_strlen($name) > 64) {
            throw new ValidationException('Vehicle name must contain between 1 and 64 characters.');
        }
    }
}
