# `Byd\ApiClient\Dto\Response\Vehicle`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/Vehicle.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `vin` | `\Byd\ApiClient\Value\Vin` | `—` |
| `modelName` | `string` | `—` |
| `brandName` | `string` | `—` |
| `energyType` | `string` | `—` |
| `alias` | `string` | `—` |
| `plate` | `string` | `—` |
| `mainImageUrl` | `string` | `—` |
| `imageSetUrl` | `string` | `—` |
| `externalModelType` | `string` | `—` |
| `totalMileage` | `?float` | `—` |
| `modelId` | `?int` | `—` |
| `carType` | `?int` | `—` |
| `defaultCar` | `string|int|bool` | `—` |
| `tboxVersion` | `string` | `—` |
| `vehicleState` | `string` | `—` |
| `raw` | `array` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, string $modelName = '', string $brandName = '', string $energyType = 'EV', string $alias = '', string $plate = '', string $mainImageUrl = '', string $imageSetUrl = '', string $externalModelType = '', ?float $totalMileage = null, ?int $modelId = null, ?int $carType = null, string|int|bool $defaultCar = false, string $tboxVersion = '3', string $vehicleState = '', array $raw = []): mixed
```

### `isDefault`

_No PHPDoc description provided._

```php
public function isDefault(): bool
```

