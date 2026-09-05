# `Byd\ApiClient\Dto\Request\RemoteControlRequest`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Request/RemoteControlRequest.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `vin` | `\Byd\ApiClient\Value\Vin` | `—` |
| `command` | `\Byd\ApiClient\Enum\RemoteCommand` | `—` |
| `commandPassword` | `string` | `—` |
| `parameters` | `?array` | `—` |
| `requestSerial` | `?string` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, \Byd\ApiClient\Enum\RemoteCommand $command, string $commandPassword = '', ?array $parameters = null, ?string $requestSerial = null): mixed
```

