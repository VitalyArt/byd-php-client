# `Byd\ApiClient\Config\WatchClientConfig`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Config/WatchClientConfig.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `device` | `\Byd\ApiClient\Config\WatchDeviceProfile` | `—` |
| `locale` | `\Byd\ApiClient\Config\Locale` | `—` |
| `baseUrl` | `?string` | `—` |
| `polling` | `\Byd\ApiClient\Policy\PollingPolicy` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Config\WatchDeviceProfile $device, \Byd\ApiClient\Config\Locale $locale = new \Byd\ApiClient\Config\Locale(...), ?string $baseUrl = null, \Byd\ApiClient\Policy\PollingPolicy $polling = new \Byd\ApiClient\Policy\PollingPolicy(...)): mixed
```

### `resolvedBaseUrl`

_No PHPDoc description provided._

```php
public function resolvedBaseUrl(): string
```

