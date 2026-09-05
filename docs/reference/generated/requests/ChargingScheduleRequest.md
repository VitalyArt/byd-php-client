# `Byd\ApiClient\Dto\Request\ChargingScheduleRequest`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Request/ChargingScheduleRequest.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `vin` | `\Byd\ApiClient\Value\Vin` | `—` |
| `targetSoc` | `int` | `—` |
| `startHour` | `int` | `—` |
| `startMinute` | `int` | `—` |
| `endHour` | `int` | `—` |
| `endMinute` | `int` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, int $targetSoc, int $startHour, int $startMinute, int $endHour, int $endMinute): mixed
```

