# `Byd\ApiClient\Dto\Response\ChargingStatus`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/ChargingStatus.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `state` | `\Byd\ApiClient\Enum\ChargingState` | `—` |
| `chargerPower` | `?float` | `—` |
| `chargerVoltage` | `?float` | `—` |
| `chargerCurrent` | `?float` | `—` |
| `batteryCapacity` | `?float` | `—` |
| `batteryTemperature` | `?float` | `—` |
| `stateOfCharge` | `?float` | `—` |
| `chargingPower` | `?float` | `—` |
| `chargingTime` | `?int` | `—` |
| `remainingTime` | `?int` | `—` |
| `chargerName` | `?string` | `—` |
| `chargerSerial` | `?string` | `—` |
| `totalFee` | `?float` | `—` |
| `connectionState` | `\Byd\ApiClient\Enum\ChargingConnectionState` | `—` |
| `hoursToFull` | `?int` | `—` |
| `minutesToFull` | `?int` | `—` |
| `electricSocLimit` | `?int` | `—` |
| `hybridSocLimit` | `?int` | `—` |
| `batteryHeatState` | `?int` | `—` |
| `raw` | `array` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Enum\ChargingState $state = Byd\ApiClient\Enum\ChargingState::UNKNOWN, ?float $chargerPower = null, ?float $chargerVoltage = null, ?float $chargerCurrent = null, ?float $batteryCapacity = null, ?float $batteryTemperature = null, ?float $stateOfCharge = null, ?float $chargingPower = null, ?int $chargingTime = null, ?int $remainingTime = null, ?string $chargerName = null, ?string $chargerSerial = null, ?float $totalFee = null, \Byd\ApiClient\Enum\ChargingConnectionState $connectionState = Byd\ApiClient\Enum\ChargingConnectionState::UNKNOWN, ?int $hoursToFull = null, ?int $minutesToFull = null, ?int $electricSocLimit = null, ?int $hybridSocLimit = null, ?int $batteryHeatState = null, array $raw = []): mixed
```

