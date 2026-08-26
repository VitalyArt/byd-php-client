<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Byd\ApiClient\Enum\ClimateMode;
use Byd\ApiClient\Enum\SeatClimateLevel;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class ClimateStatus
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('airConditioningMode')]
        public ClimateMode $mode = ClimateMode::UNKNOWN,
        #[SerializedName('acSwitch')]
        public ?int $switch = null,
        #[SerializedName('tempInCar')]
        public ?float $interiorTemperature = null,
        #[SerializedName('tempOutCar')]
        public ?float $exteriorTemperature = null,
        #[SerializedName('mainSettingTempNew')]
        public ?float $driverTemperature = null,
        #[SerializedName('copilotSettingTempNew')]
        public ?float $passengerTemperature = null,
        #[SerializedName('mainSeatHeatState')]
        public SeatClimateLevel $driverSeatHeat = SeatClimateLevel::UNKNOWN,
        #[SerializedName('mainSeatVentilationState')]
        public SeatClimateLevel $driverSeatVentilation = SeatClimateLevel::UNKNOWN,
        #[SerializedName('copilotSeatHeatState')]
        public SeatClimateLevel $passengerSeatHeat = SeatClimateLevel::UNKNOWN,
        #[SerializedName('copilotSeatVentilationState')]
        public SeatClimateLevel $passengerSeatVentilation = SeatClimateLevel::UNKNOWN,
        #[SerializedName('steeringWheelHeatState')]
        public ?int $steeringWheelHeat = null,
        #[SerializedName('pm')]
        public ?float $particulateMatter = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }

    public function isOn(): bool
    {
        return $this->switch === 1 || $this->switch === 2;
    }
}
