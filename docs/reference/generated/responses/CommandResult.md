# `Byd\ApiClient\Dto\Response\CommandResult`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/CommandResult.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `code` | `string|int` | `—` |
| `message` | `string` | `—` |
| `requestSerial` | `?string` | `—` |
| `controlState` | `?int` | `—` |
| `result` | `?int` | `—` |
| `commandType` | `?string` | `—` |
| `raw` | `array` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(string|int $code = '0', string $message = '', ?string $requestSerial = null, ?int $controlState = null, ?int $result = null, ?string $commandType = null, array $raw = []): mixed
```

### `isSuccess`

_No PHPDoc description provided._

```php
public function isSuccess(): bool
```

### `isTerminal`

_No PHPDoc description provided._

```php
public function isTerminal(): bool
```

