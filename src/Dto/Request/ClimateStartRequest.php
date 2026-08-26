<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Request;

use Byd\ApiClient\Exception\ValidationException;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class ClimateStartRequest
{
    public function __construct(
        #[SerializedName('mainSettingTemp')]
        public ?float $temperature = null,
        #[SerializedName('copilotSettingTemp')]
        public ?float $passengerTemperature = null,
        #[SerializedName('cycleMode')]
        public int $cycleMode = 2,
        #[SerializedName('timeSpan')]
        public ?int $durationMinutes = null,
        #[SerializedName('remoteMode')]
        public int $remoteMode = 4,
        #[SerializedName('airAccuracy')]
        public int $airAccuracy = 1,
        #[SerializedName('airConditioningMode')]
        public int $mode = 1,
        #[SerializedName('windLevel')]
        public ?int $windLevel = null,
        #[SerializedName('airSet')]
        public ?string $airSet = null,
    ) {
        foreach ([$temperature, $passengerTemperature] as $value) {
            if ($value !== null && ($value < 15.0 || $value > 30.0)) {
                throw new ValidationException('Climate temperature must be between 15 and 30 °C.');
            }
        }

        if ($durationMinutes !== null && ($durationMinutes < 1 || $durationMinutes > 60)) {
            throw new ValidationException('Climate duration must be between 1 and 60 minutes.');
        }
    }

}
