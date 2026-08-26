<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Exception\ValidationException;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class ClimateScheduleCommand
{
    public function __construct(
        #[SerializedName('startHour')]
        public int $startHour,
        #[SerializedName('startMinute')]
        public int $startMinute,
        #[SerializedName('endHour')]
        public int $endHour,
        #[SerializedName('endMinute')]
        public int $endMinute,
        #[SerializedName('temperature')]
        public ?int $temperature = null,
        #[SerializedName('seatHeating')]
        public ?int $seatHeating = null,
        #[SerializedName('seatVentilation')]
        public ?int $seatVentilation = null,
        #[SerializedName('steeringWheelHeating')]
        public ?bool $steeringWheelHeating = null,
    ) {
        if ($startHour < 0 || $startHour > 23 || $endHour < 0 || $endHour > 23 || $startMinute < 0 || $startMinute > 59 || $endMinute < 0 || $endMinute > 59) {
            throw new ValidationException('Invalid climate schedule time.');
        }
    }
}
