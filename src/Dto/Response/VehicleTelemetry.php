<?php

declare(strict_types=1);

namespace Byd\ApiClient\Dto\Response;

use Byd\ApiClient\Enum\ChargingState;
use Byd\ApiClient\Enum\DoorState;
use Byd\ApiClient\Enum\LockState;
use Byd\ApiClient\Enum\OnlineState;
use Byd\ApiClient\Enum\WindowState;

use function in_array;

use Symfony\Component\Serializer\Attribute\Ignore;
use Symfony\Component\Serializer\Attribute\SerializedName;

final readonly class VehicleTelemetry
{
    /** @param array<string, mixed> $raw */
    public function __construct(
        #[SerializedName('requestSerial')]
        public ?string $requestSerial = null,
        #[SerializedName('onlineState')]
        public OnlineState $onlineState = OnlineState::UNKNOWN,
        #[SerializedName('chargingState')]
        public ChargingState $chargingState = ChargingState::UNKNOWN,
        #[SerializedName('elecPercent')]
        public ?float $stateOfCharge = null,
        #[SerializedName('powerBattery')]
        public ?float $batteryPower = null,
        #[SerializedName('enduranceMileage')]
        public ?float $remainingRange = null,
        #[SerializedName('totalMileage')]
        public ?float $totalMileage = null,
        #[SerializedName('speed')]
        public ?float $speed = null,
        #[SerializedName('tempInCar')]
        public ?float $interiorTemperature = null,
        #[SerializedName('tempOutCar')]
        public ?float $exteriorTemperature = null,
        #[SerializedName('leftFrontDoor')]
        public DoorState $leftFrontDoor = DoorState::UNKNOWN,
        #[SerializedName('rightFrontDoor')]
        public DoorState $rightFrontDoor = DoorState::UNKNOWN,
        #[SerializedName('leftRearDoor')]
        public DoorState $leftRearDoor = DoorState::UNKNOWN,
        #[SerializedName('rightRearDoor')]
        public DoorState $rightRearDoor = DoorState::UNKNOWN,
        #[SerializedName('trunkLid')]
        public DoorState $trunk = DoorState::UNKNOWN,
        #[SerializedName('leftFrontDoorLock')]
        public LockState $leftFrontLock = LockState::UNKNOWN,
        #[SerializedName('rightFrontDoorLock')]
        public LockState $rightFrontLock = LockState::UNKNOWN,
        #[SerializedName('leftRearDoorLock')]
        public LockState $leftRearLock = LockState::UNKNOWN,
        #[SerializedName('rightRearDoorLock')]
        public LockState $rightRearLock = LockState::UNKNOWN,
        #[SerializedName('leftFrontWindow')]
        public WindowState $leftFrontWindow = WindowState::UNKNOWN,
        #[SerializedName('rightFrontWindow')]
        public WindowState $rightFrontWindow = WindowState::UNKNOWN,
        #[SerializedName('leftRearWindow')]
        public WindowState $leftRearWindow = WindowState::UNKNOWN,
        #[SerializedName('rightRearWindow')]
        public WindowState $rightRearWindow = WindowState::UNKNOWN,
        #[SerializedName('leftFrontTirePressure')]
        public ?float $leftFrontTirePressure = null,
        #[SerializedName('rightFrontTirePressure')]
        public ?float $rightFrontTirePressure = null,
        #[SerializedName('leftRearTirePressure')]
        public ?float $leftRearTirePressure = null,
        #[SerializedName('rightRearTirePressure')]
        public ?float $rightRearTirePressure = null,
        #[SerializedName('fullHour')]
        public ?int $hoursToFull = null,
        #[SerializedName('fullMinute')]
        public ?int $minutesToFull = null,
        #[SerializedName('oilEndurance')]
        public ?float $fuelRange = null,
        #[SerializedName('oilPercent')]
        public ?float $fuelPercent = null,
        #[SerializedName('batteryHeatState')]
        public ?int $batteryHeatState = null,
        #[SerializedName('steeringWheelHeatState')]
        public ?int $steeringWheelHeatState = null,
        #[SerializedName('timestamp')]
        public string|int|null $timestamp = null,
        #[Ignore]
        public array $raw = [],
    ) {
    }

    public function isOnline(): bool
    {
        return $this->onlineState === OnlineState::ONLINE;
    }

    public function isCharging(): bool
    {
        return $this->chargingState === ChargingState::CHARGING;
    }

    public function isAnyDoorOpen(): bool
    {
        return in_array(DoorState::OPEN, [$this->leftFrontDoor, $this->rightFrontDoor, $this->leftRearDoor, $this->rightRearDoor, $this->trunk], true);
    }

    public function isAnyWindowOpen(): bool
    {
        return in_array(WindowState::OPEN, [$this->leftFrontWindow, $this->rightFrontWindow, $this->leftRearWindow, $this->rightRearWindow], true);
    }

    public function hasMeaningfulData(): bool
    {
        foreach ([$this->leftFrontTirePressure, $this->rightFrontTirePressure, $this->leftRearTirePressure, $this->rightRearTirePressure] as $pressure) {
            if ($pressure !== null && $pressure > 0.0) {
                return true;
            }
        }

        if (is_numeric($this->timestamp) && (int) $this->timestamp > 0) {
            return true;
        }

        return $this->remainingRange !== null && $this->remainingRange > 0.0;
    }
}
