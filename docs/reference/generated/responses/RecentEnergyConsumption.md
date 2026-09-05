# `Byd\ApiClient\Dto\Response\RecentEnergyConsumption`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/RecentEnergyConsumption.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `averageElectricConsumption` | `?float` | `—` |
| `electricConsumption` | `?float` | `—` |
| `averageElectricConsumptionUnit` | `?string` | `—` |
| `electricConsumptionUnit` | `?string` | `—` |
| `averageFuelConsumption` | `string|float|null` | `—` |
| `fuelConsumption` | `string|float|null` | `—` |
| `raw` | `array` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(?float $averageElectricConsumption = null, ?float $electricConsumption = null, ?string $averageElectricConsumptionUnit = null, ?string $electricConsumptionUnit = null, string|float|null $averageFuelConsumption = null, string|float|null $fuelConsumption = null, array $raw = []): mixed
```

