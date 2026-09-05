# `Byd\ApiClient\Dto\Response\EnergyConsumption`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/EnergyConsumption.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `cumulative` | `?\Byd\ApiClient\Dto\Response\CumulativeEnergyConsumption` | `—` |
| `recent` | `?\Byd\ApiClient\Dto\Response\RecentEnergyConsumption` | `—` |
| `vehicleGraph` | `?\Byd\ApiClient\Dto\Response\EnergyConsumptionGraph` | `—` |
| `modelGraph` | `?\Byd\ApiClient\Dto\Response\EnergyConsumptionGraph` | `—` |
| `totalMileage` | `?float` | `—` |
| `totalEnergy` | `?float` | `—` |
| `recentAverage` | `?float` | `—` |
| `recent50Km` | `string|float|null` | `—` |
| `drivingEnergy` | `?float` | `—` |
| `chargingEnergy` | `?float` | `—` |
| `electricMileage` | `?float` | `—` |
| `fuelMileage` | `?float` | `—` |
| `co2Emission` | `?float` | `—` |
| `co2Saved` | `?float` | `—` |
| `mileageUnit` | `?string` | `—` |
| `cumulativeAverageEvConsumption` | `?float` | `—` |
| `cumulativeEvUnit` | `?string` | `—` |
| `timestamp` | `string|int|null` | `—` |
| `raw` | `array` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(?\Byd\ApiClient\Dto\Response\CumulativeEnergyConsumption $cumulative = null, ?\Byd\ApiClient\Dto\Response\RecentEnergyConsumption $recent = null, ?\Byd\ApiClient\Dto\Response\EnergyConsumptionGraph $vehicleGraph = null, ?\Byd\ApiClient\Dto\Response\EnergyConsumptionGraph $modelGraph = null, ?float $totalMileage = null, ?float $totalEnergy = null, ?float $recentAverage = null, string|float|null $recent50Km = null, ?float $drivingEnergy = null, ?float $chargingEnergy = null, ?float $electricMileage = null, ?float $fuelMileage = null, ?float $co2Emission = null, ?float $co2Saved = null, ?string $mileageUnit = null, ?float $cumulativeAverageEvConsumption = null, ?string $cumulativeEvUnit = null, string|int|null $timestamp = null, array $raw = []): mixed
```

