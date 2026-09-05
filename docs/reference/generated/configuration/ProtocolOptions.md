# `Byd\ApiClient\Config\ProtocolOptions`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Config/ProtocolOptions.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `baseUrl` | `string` | `—` |
| `appName` | `string` | `—` |
| `appVersion` | `string` | `—` |
| `appInnerVersion` | `string` | `—` |
| `softType` | `string` | `—` |
| `tboxVersion` | `string` | `—` |
| `automaticLogin` | `bool` | `—` |

## Public methods

### `forCountry`

_No PHPDoc description provided._

```php
public static function forCountry(\Byd\ApiClient\Enum\CountryCode $countryCode): \Byd\ApiClient\Config\ProtocolOptions
```

### `forRegion`

_No PHPDoc description provided._

```php
public static function forRegion(\Byd\ApiClient\Enum\ApiRegion $region): \Byd\ApiClient\Config\ProtocolOptions
```

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(string $baseUrl = 'https://dilinkappoversea-eu.byd.auto', string $appName = 'pyBYD+0.0.73', string $appVersion = '3.5.0', string $appInnerVersion = '352', string $softType = '0', string $tboxVersion = '3', bool $automaticLogin = true): mixed
```

