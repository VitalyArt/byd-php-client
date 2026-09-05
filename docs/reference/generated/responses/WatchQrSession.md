# `Byd\ApiClient\Dto\Response\WatchQrSession`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/WatchQrSession.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `watchImei` | `string` | `—` |
| `uuid` | `string` | `—` |
| `status` | `\Byd\ApiClient\Enum\WatchQrStatus` | `—` |
| `qrPayload` | `string` | `—` |
| `createdAtMilliseconds` | `int` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(string $watchImei, string $uuid, \Byd\ApiClient\Enum\WatchQrStatus $status, string $qrPayload, int $createdAtMilliseconds): mixed
```

### `expiresAtMilliseconds`

_No PHPDoc description provided._

```php
public function expiresAtMilliseconds(): int
```

