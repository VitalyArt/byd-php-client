<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Value\Vin;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class StartChargingRequest
{
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('status')]
        public string $status = '1',
    ) {
    }
}
