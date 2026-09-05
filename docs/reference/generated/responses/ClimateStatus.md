# `Byd\ApiClient\Dto\Response\ClimateStatus`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/ClimateStatus.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `mode` | `\Byd\ApiClient\Enum\ClimateMode` | `—` |
| `switch` | `?int` | `—` |
| `interiorTemperature` | `?float` | `—` |
| `exteriorTemperature` | `?float` | `—` |
| `driverTemperature` | `?float` | `—` |
| `passengerTemperature` | `?float` | `—` |
| `driverSeatHeat` | `\Byd\ApiClient\Enum\SeatClimateLevel` | `—` |
| `driverSeatVentilation` | `\Byd\ApiClient\Enum\SeatClimateLevel` | `—` |
| `passengerSeatHeat` | `\Byd\ApiClient\Enum\SeatClimateLevel` | `—` |
| `passengerSeatVentilation` | `\Byd\ApiClient\Enum\SeatClimateLevel` | `—` |
| `steeringWheelHeat` | `?int` | `—` |
| `particulateMatter` | `?float` | `—` |
| `raw` | `array` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Enum\ClimateMode $mode = Byd\ApiClient\Enum\ClimateMode::UNKNOWN, ?int $switch = null, ?float $interiorTemperature = null, ?float $exteriorTemperature = null, ?float $driverTemperature = null, ?float $passengerTemperature = null, \Byd\ApiClient\Enum\SeatClimateLevel $driverSeatHeat = Byd\ApiClient\Enum\SeatClimateLevel::UNKNOWN, \Byd\ApiClient\Enum\SeatClimateLevel $driverSeatVentilation = Byd\ApiClient\Enum\SeatClimateLevel::UNKNOWN, \Byd\ApiClient\Enum\SeatClimateLevel $passengerSeatHeat = Byd\ApiClient\Enum\SeatClimateLevel::UNKNOWN, \Byd\ApiClient\Enum\SeatClimateLevel $passengerSeatVentilation = Byd\ApiClient\Enum\SeatClimateLevel::UNKNOWN, ?int $steeringWheelHeat = null, ?float $particulateMatter = null, array $raw = []): mixed
```

### `isOn`

_No PHPDoc description provided._

```php
public function isOn(): bool
```

