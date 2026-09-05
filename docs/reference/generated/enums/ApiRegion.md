# `Byd\ApiClient\Enum\ApiRegion`

_No PHPDoc description provided._

**Type:** `enum`  
**Source:** `src/Enum/ApiRegion.php`

## Cases

| Case | Backed value |
| --- | --- |
| `EUROPE` | `https://dilinkappoversea-eu.byd.auto` |
| `SOUTHEAST_ASIA` | `https://dilinkappoversea-sg.byd.auto` |
| `OCEANIA` | `https://dilinkappoversea-au.byd.auto` |
| `BRAZIL` | `https://dilinkappoversea-br.byd.auto` |
| `JAPAN` | `https://dilinkappoversea-jp.byd.auto` |
| `UZBEKISTAN` | `https://dilinkappoversea-uz.byd.auto` |
| `MIDDLE_EAST_AFRICA` | `https://dilinkappoversea-no.byd.auto` |
| `LATIN_AMERICA` | `https://dilinkappoversea-mx.byd.auto` |
| `INDONESIA` | `https://dilinkappoversea-id.byd.auto` |
| `TURKEY` | `https://dilinkappoversea-tr.byd.auto` |
| `SOUTH_KOREA` | `https://dilinkappoversea-kr-ali.byd.auto` |
| `INDIA` | `https://dilinkappoversea-in.byd.auto` |
| `VIETNAM` | `https://dilinkappoversea-vn.byd.auto` |
| `SAUDI_ARABIA` | `https://dilinkappoversea-sa.byd.auto` |
| `OMAN` | `https://dilinkappoversea-om.byd.auto` |
| `KAZAKHSTAN` | `https://dilinkappoversea-kz.byd.auto` |

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `name` | `string` | `—` |
| `value` | `string` | `—` |

## Public methods

### `forCountry`

_No PHPDoc description provided._

```php
public static function forCountry(\Byd\ApiClient\Enum\CountryCode $countryCode): \Byd\ApiClient\Enum\ApiRegion
```

### `node`

_No PHPDoc description provided._

```php
public function node(): int
```

### `countries`

_No PHPDoc description provided._

```php
public function countries(): array
```

