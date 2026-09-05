# `Byd\ApiClient\Dto\Response\CumulativeEnergyConsumption`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/CumulativeEnergyConsumption.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `totalMileage` | `?float` | `—` |
| `mileageUnit` | `?string` | `—` |
| `averageElectricConsumption` | `?float` | `—` |
| `electricConsumptionUnit` | `?string` | `—` |
| `averageFuelConsumption` | `string|float|null` | `—` |
| `fuelConsumptionUnit` | `?string` | `—` |
| `raw` | `array` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(?float $totalMileage = null, ?string $mileageUnit = null, ?float $averageElectricConsumption = null, ?string $electricConsumptionUnit = null, string|float|null $averageFuelConsumption = null, ?string $fuelConsumptionUnit = null, array $raw = []): mixed
```

