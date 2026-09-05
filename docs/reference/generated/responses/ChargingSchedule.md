# `Byd\ApiClient\Dto\Response\ChargingSchedule`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/ChargingSchedule.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `startTime` | `?string` | `—` |
| `endTime` | `?string` | `—` |
| `status` | `string|int|null` | `—` |
| `chargeWay` | `?string` | `—` |
| `executionTime` | `?string` | `—` |
| `raw` | `array` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(?string $startTime = null, ?string $endTime = null, string|int|null $status = null, ?string $chargeWay = null, ?string $executionTime = null, array $raw = []): mixed
```

### `isEnabled`

_No PHPDoc description provided._

```php
public function isEnabled(): ?bool
```

