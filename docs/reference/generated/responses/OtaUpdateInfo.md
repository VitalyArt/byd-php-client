# `Byd\ApiClient\Dto\Response\OtaUpdateInfo`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Dto/Response/OtaUpdateInfo.php`

## Public properties

| Property | Type | Default |
| --- | --- | --- |
| `bookingTimestamp` | `?int` | `—` |
| `currentVersion` | `string` | `—` |
| `updateVersion` | `string` | `—` |
| `estimatedUpgradeTime` | `?int` | `—` |
| `vehicleTimeZone` | `string` | `—` |
| `description` | `string` | `—` |
| `status` | `\Byd\ApiClient\Enum\OtaUpgradeStatus` | `—` |
| `addedFeatures` | `string` | `—` |
| `optimizations` | `string` | `—` |
| `acknowledgements` | `string` | `—` |
| `currentUpdateTimestamp` | `?int` | `—` |
| `upgradeResult` | `?\Byd\ApiClient\Dto\Response\OtaUpgradeResult` | `—` |
| `raw` | `array` | `—` |

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(?int $bookingTimestamp = null, string $currentVersion = '', string $updateVersion = '', ?int $estimatedUpgradeTime = null, string $vehicleTimeZone = '', string $description = '', \Byd\ApiClient\Enum\OtaUpgradeStatus $status = Byd\ApiClient\Enum\OtaUpgradeStatus::UNKNOWN, string $addedFeatures = '', string $optimizations = '', string $acknowledgements = '', ?int $currentUpdateTimestamp = null, ?\Byd\ApiClient\Dto\Response\OtaUpgradeResult $upgradeResult = null, array $raw = []): mixed
```

### `hasUpdate`

_No PHPDoc description provided._

```php
public function hasUpdate(): bool
```

