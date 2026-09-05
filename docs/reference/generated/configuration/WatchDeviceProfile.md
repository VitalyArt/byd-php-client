# `Byd\ApiClient\Config\WatchDeviceProfile`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Config/WatchDeviceProfile.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `watchImei` | `string` | `—` |
| `brand` | `string` | `—` |
| `model` | `string` | `—` |
| `appVersion` | `string` | `—` |
| `watchOs` | `string` | `—` |
| `networkType` | `string` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(string $watchImei, string $brand, string $model, string $appVersion = '341', string $watchOs = '0', string $networkType = 'wifi'): mixed
```

### `generate`

_No PHPDoc description provided._

```php
public static function generate(string $brand, string $model): \Byd\ApiClient\Config\WatchDeviceProfile
```

### `watchName`

_No PHPDoc description provided._

```php
public function watchName(): string
```

