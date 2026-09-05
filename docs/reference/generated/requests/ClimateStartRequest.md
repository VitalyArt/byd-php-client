# `Byd\ApiClient\Dto\Request\ClimateStartRequest`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Request/ClimateStartRequest.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `temperature` | `?float` | `—` |
| `passengerTemperature` | `?float` | `—` |
| `cycleMode` | `int` | `—` |
| `durationMinutes` | `?int` | `—` |
| `remoteMode` | `int` | `—` |
| `airAccuracy` | `int` | `—` |
| `mode` | `int` | `—` |
| `windLevel` | `?int` | `—` |
| `airSet` | `?string` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(?float $temperature = null, ?float $passengerTemperature = null, int $cycleMode = 2, ?int $durationMinutes = null, int $remoteMode = 4, int $airAccuracy = 1, int $mode = 1, ?int $windLevel = null, ?string $airSet = null): mixed
```

