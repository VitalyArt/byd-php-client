<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Enum\SeatClimateLevel;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class SeatClimateRequest
{
    public function __construct(
        #[SerializedName('seatHeating')]
        public ?SeatClimateLevel $heating = null,
        #[SerializedName('seatVentilation')]
        public ?SeatClimateLevel $ventilation = null,
    ) {
    }
}
