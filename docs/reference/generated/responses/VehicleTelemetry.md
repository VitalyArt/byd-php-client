# `Byd\ApiClient\Dto\Response\VehicleTelemetry`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/VehicleTelemetry.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `requestSerial` | `?string` | `—` |
| `onlineState` | `\Byd\ApiClient\Enum\OnlineState` | `—` |
| `chargingState` | `\Byd\ApiClient\Enum\ChargingState` | `—` |
| `stateOfCharge` | `?float` | `—` |
| `batteryPower` | `?float` | `—` |
| `remainingRange` | `?float` | `—` |
| `totalMileage` | `?float` | `—` |
| `speed` | `?float` | `—` |
| `interiorTemperature` | `?float` | `—` |
| `exteriorTemperature` | `?float` | `—` |
| `leftFrontDoor` | `\Byd\ApiClient\Enum\DoorState` | `—` |
| `rightFrontDoor` | `\Byd\ApiClient\Enum\DoorState` | `—` |
| `leftRearDoor` | `\Byd\ApiClient\Enum\DoorState` | `—` |
| `rightRearDoor` | `\Byd\ApiClient\Enum\DoorState` | `—` |
| `trunk` | `\Byd\ApiClient\Enum\DoorState` | `—` |
| `leftFrontLock` | `\Byd\ApiClient\Enum\LockState` | `—` |
| `rightFrontLock` | `\Byd\ApiClient\Enum\LockState` | `—` |
| `leftRearLock` | `\Byd\ApiClient\Enum\LockState` | `—` |
| `rightRearLock` | `\Byd\ApiClient\Enum\LockState` | `—` |
| `leftFrontWindow` | `\Byd\ApiClient\Enum\WindowState` | `—` |
| `rightFrontWindow` | `\Byd\ApiClient\Enum\WindowState` | `—` |
| `leftRearWindow` | `\Byd\ApiClient\Enum\WindowState` | `—` |
| `rightRearWindow` | `\Byd\ApiClient\Enum\WindowState` | `—` |
| `leftFrontTirePressure` | `?float` | `—` |
| `rightFrontTirePressure` | `?float` | `—` |
| `leftRearTirePressure` | `?float` | `—` |
| `rightRearTirePressure` | `?float` | `—` |
| `hoursToFull` | `?int` | `—` |
| `minutesToFull` | `?int` | `—` |
| `fuelRange` | `?float` | `—` |
| `fuelPercent` | `?float` | `—` |
| `batteryHeatState` | `?int` | `—` |
| `steeringWheelHeatState` | `?int` | `—` |
| `timestamp` | `string|int|null` | `—` |
| `raw` | `array` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(?string $requestSerial = null, \Byd\ApiClient\Enum\OnlineState $onlineState = Byd\ApiClient\Enum\OnlineState::UNKNOWN, \Byd\ApiClient\Enum\ChargingState $chargingState = Byd\ApiClient\Enum\ChargingState::UNKNOWN, ?float $stateOfCharge = null, ?float $batteryPower = null, ?float $remainingRange = null, ?float $totalMileage = null, ?float $speed = null, ?float $interiorTemperature = null, ?float $exteriorTemperature = null, \Byd\ApiClient\Enum\DoorState $leftFrontDoor = Byd\ApiClient\Enum\DoorState::UNKNOWN, \Byd\ApiClient\Enum\DoorState $rightFrontDoor = Byd\ApiClient\Enum\DoorState::UNKNOWN, \Byd\ApiClient\Enum\DoorState $leftRearDoor = Byd\ApiClient\Enum\DoorState::UNKNOWN, \Byd\ApiClient\Enum\DoorState $rightRearDoor = Byd\ApiClient\Enum\DoorState::UNKNOWN, \Byd\ApiClient\Enum\DoorState $trunk = Byd\ApiClient\Enum\DoorState::UNKNOWN, \Byd\ApiClient\Enum\LockState $leftFrontLock = Byd\ApiClient\Enum\LockState::UNKNOWN, \Byd\ApiClient\Enum\LockState $rightFrontLock = Byd\ApiClient\Enum\LockState::UNKNOWN, \Byd\ApiClient\Enum\LockState $leftRearLock = Byd\ApiClient\Enum\LockState::UNKNOWN, \Byd\ApiClient\Enum\LockState $rightRearLock = Byd\ApiClient\Enum\LockState::UNKNOWN, \Byd\ApiClient\Enum\WindowState $leftFrontWindow = Byd\ApiClient\Enum\WindowState::UNKNOWN, \Byd\ApiClient\Enum\WindowState $rightFrontWindow = Byd\ApiClient\Enum\WindowState::UNKNOWN, \Byd\ApiClient\Enum\WindowState $leftRearWindow = Byd\ApiClient\Enum\WindowState::UNKNOWN, \Byd\ApiClient\Enum\WindowState $rightRearWindow = Byd\ApiClient\Enum\WindowState::UNKNOWN, ?float $leftFrontTirePressure = null, ?float $rightFrontTirePressure = null, ?float $leftRearTirePressure = null, ?float $rightRearTirePressure = null, ?int $hoursToFull = null, ?int $minutesToFull = null, ?float $fuelRange = null, ?float $fuelPercent = null, ?int $batteryHeatState = null, ?int $steeringWheelHeatState = null, string|int|null $timestamp = null, array $raw = []): mixed
```

### `isOnline`

_No PHPDoc description provided._

```php
public function isOnline(): bool
```

### `isCharging`

_No PHPDoc description provided._

```php
public function isCharging(): bool
```

### `isAnyDoorOpen`

_No PHPDoc description provided._

```php
public function isAnyDoorOpen(): bool
```

### `isAnyWindowOpen`

_No PHPDoc description provided._

```php
public function isAnyWindowOpen(): bool
```

### `hasMeaningfulData`

_No PHPDoc description provided._

```php
public function hasMeaningfulData(): bool
```

