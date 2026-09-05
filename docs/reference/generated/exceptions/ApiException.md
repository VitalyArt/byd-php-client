# `Byd\ApiClient\Exception\ApiException`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Exception/ApiException.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `apiCode` | `int` | `—` |
| `endpoint` | `\Byd\ApiClient\Enum\Endpoint` | `—` |
| `knownCode` | `?\Byd\ApiClient\Enum\ApiErrorCode` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(string $message, int $apiCode, \Byd\ApiClient\Enum\Endpoint $endpoint, ?\Byd\ApiClient\Enum\ApiErrorCode $knownCode = null, ?\Throwable $previous = null): mixed
```

