# `Byd\ApiClient\Dto\Request\TelemetryRequest`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Request/TelemetryRequest.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `vin` | `\Byd\ApiClient\Value\Vin` | `—` |
| `energyType` | `\Byd\ApiClient\Enum\EnergyType` | `—` |
| `tboxVersion` | `string` | `—` |
| `requestSerial` | `?string` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, \Byd\ApiClient\Enum\EnergyType $energyType, string $tboxVersion, ?string $requestSerial = null): mixed
```

