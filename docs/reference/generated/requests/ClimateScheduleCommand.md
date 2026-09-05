# `Byd\ApiClient\Dto\Request\ClimateScheduleCommand`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Request/ClimateScheduleCommand.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `startHour` | `int` | `—` |
| `startMinute` | `int` | `—` |
| `endHour` | `int` | `—` |
| `endMinute` | `int` | `—` |
| `temperature` | `?int` | `—` |
| `seatHeating` | `?int` | `—` |
| `seatVentilation` | `?int` | `—` |
| `steeringWheelHeating` | `?bool` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(int $startHour, int $startMinute, int $endHour, int $endMinute, ?int $temperature = null, ?int $seatHeating = null, ?int $seatVentilation = null, ?bool $steeringWheelHeating = null): mixed
```

