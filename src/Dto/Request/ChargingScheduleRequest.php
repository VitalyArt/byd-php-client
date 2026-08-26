<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Exception\ValidationException;
use Byd\ApiClient\Value\Vin;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class ChargingScheduleRequest
{
    public function __construct(
        #[SerializedName('vin')]
        public Vin $vin,
        #[SerializedName('targetSoc')]
        public int $targetSoc,
        #[SerializedName('startHour')]
        public int $startHour,
        #[SerializedName('startMinute')]
        public int $startMinute,
        #[SerializedName('endHour')]
        public int $endHour,
        #[SerializedName('endMinute')]
        public int $endMinute,
    ) {
        if ($targetSoc < 0 || $targetSoc > 100 || $startHour < 0 || $startHour > 23 || $endHour < 0 || $endHour > 23 || $startMinute < 0 || $startMinute > 59 || $endMinute < 0 || $endMinute > 59) {
            throw new ValidationException('Invalid charging schedule values.');
        }
    }
}
