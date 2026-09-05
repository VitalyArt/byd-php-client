# `Byd\ApiClient\Config\ClientConfig`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Config/ClientConfig.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `credentials` | `\Byd\ApiClient\Config\Credentials` | `—` |
| `locale` | `\Byd\ApiClient\Config\Locale` | `—` |
| `device` | `\Byd\ApiClient\Config\DeviceProfile` | `—` |
| `protocol` | `\Byd\ApiClient\Config\ProtocolOptions` | `—` |
| `polling` | `\Byd\ApiClient\Policy\PollingPolicy` | `—` |
| `authenticationRetry` | `\Byd\ApiClient\Policy\AuthenticationRetryPolicy` | `—` |
| `sessionTtlSeconds` | `int` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Config\Credentials $credentials, \Byd\ApiClient\Config\Locale $locale = new \Byd\ApiClient\Config\Locale(...), \Byd\ApiClient\Config\DeviceProfile $device = new \Byd\ApiClient\Config\DeviceProfile(...), \Byd\ApiClient\Config\ProtocolOptions $protocol = new \Byd\ApiClient\Config\ProtocolOptions(...), \Byd\ApiClient\Policy\PollingPolicy $polling = new \Byd\ApiClient\Policy\PollingPolicy(...), \Byd\ApiClient\Policy\AuthenticationRetryPolicy $authenticationRetry = new \Byd\ApiClient\Policy\AuthenticationRetryPolicy(...), int $sessionTtlSeconds = 43200): mixed
```

