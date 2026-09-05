# `Byd\ApiClient\Enum\WatchQrStatus`

_No PHPDoc description provided._

**Type:** `enum`  
**Source:** `src/Enum/WatchQrStatus.php`

## Cases

| Case | Backed value |
| --- | --- |
| `UNKNOWN` | `-1` |
| `WAITING_FOR_SCAN` | `0` |
| `WAITING_FOR_CONFIRMATION` | `1` |
| `APPROVED` | `2` |
| `INVALIDATED` | `3` |
| `EXPIRED` | `4` |

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `name` | `string` | `—` |
| `value` | `int` | `—` |

## Public methods

### `isTerminal`

_No PHPDoc description provided._

```php
public function isTerminal(): bool
```

