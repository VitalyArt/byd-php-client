<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Byd\ApiClient\Enum\ChargingConnectionState;
use Byd\ApiClient\Enum\ChargingState;
use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class ChargingStatus
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('chargingState')]
        public ChargingState $state = ChargingState::UNKNOWN,
        #[SerializedName('chargerPower')]
        public ?float $chargerPower = null,
        #[SerializedName('chargerVoltage')]
        public ?float $chargerVoltage = null,
        #[SerializedName('chargerCurrent')]
        public ?float $chargerCurrent = null,
        #[SerializedName('batteryCapacity')]
        public ?float $batteryCapacity = null,
        #[SerializedName('batteryTemperature')]
        public ?float $batteryTemperature = null,
        #[SerializedName('soc')]
        public ?float $stateOfCharge = null,
        #[SerializedName('chargingPower')]
        public ?float $chargingPower = null,
        #[SerializedName('chargingTime')]
        public ?int $chargingTime = null,
        #[SerializedName('remainingTime')]
        public ?int $remainingTime = null,
        #[SerializedName('chargingPileName')]
        public ?string $chargerName = null,
        #[SerializedName('chargingPileSN')]
        public ?string $chargerSerial = null,
        #[SerializedName('totalFee')]
        public ?float $totalFee = null,
        #[SerializedName('connectState')]
        public ChargingConnectionState $connectionState = ChargingConnectionState::UNKNOWN,
        #[SerializedName('fullHour')]
        public ?int $hoursToFull = null,
        #[SerializedName('fullMinute')]
        public ?int $minutesToFull = null,
        #[SerializedName('evSocLimit')]
        public ?int $electricSocLimit = null,
        #[SerializedName('dmSocLimit')]
        public ?int $hybridSocLimit = null,
        #[SerializedName('batteryHeatState')]
        public ?int $batteryHeatState = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }
}
