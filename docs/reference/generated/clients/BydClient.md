# `Byd\ApiClient\BydClient`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/BydClient.php`

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(string $username, string $password, \Byd\ApiClient\Enum\CountryCode $countryCode = Byd\ApiClient\Enum\CountryCode::NL, string $language = 'en', string $timeZone = 'Europe/Amsterdam', ?string $controlPin = null, ?\Psr\Http\Client\ClientInterface $httpClient = null, (Psr\Http\Message\RequestFactoryInterface&Psr\Http\Message\StreamFactoryInterface)|null $httpFactory = null, ?\Psr\Log\LoggerInterface $logger = null, ?\Psr\Clock\ClockInterface $clock = null, ?\Byd\ApiClient\Contract\SleeperInterface $sleeper = null, ?\Byd\ApiClient\Contract\NonceGeneratorInterface $nonceGenerator = null, ?\Byd\ApiClient\Contract\SecureTransportInterface $transport = null, ?\Byd\ApiClient\Serialization\DtoSerializer $serializer = null): mixed
```

### `authenticate`

Authenticate the BYD account and refresh the current session.

```php
public function authenticate(): void
```

### `invalidateSession`

Discard the current account session.

```php
public function invalidateSession(): void
```

### `vehicles`

Return the account vehicle service.

```php
public function vehicles(): \Byd\ApiClient\Service\VehicleService
```

### `telemetry`

Return telemetry operations for a vehicle VIN.

```php
public function telemetry(\Byd\ApiClient\Value\Vin $vin): \Byd\ApiClient\Service\TelemetryService
```

### `climate`

Return climate operations for a vehicle VIN.

```php
public function climate(\Byd\ApiClient\Value\Vin $vin): \Byd\ApiClient\Service\ClimateService
```

### `charging`

Return charging operations for a vehicle VIN.

```php
public function charging(\Byd\ApiClient\Value\Vin $vin): \Byd\ApiClient\Service\ChargingService
```

### `controls`

Return remote-control operations for a vehicle VIN.

```php
public function controls(\Byd\ApiClient\Value\Vin $vin): \Byd\ApiClient\Service\ControlService
```

### `notifications`

Return push-notification operations for a vehicle VIN.

```php
public function notifications(\Byd\ApiClient\Value\Vin $vin): \Byd\ApiClient\Service\NotificationService
```

### `settings`

Return vehicle settings operations for a vehicle VIN.

```php
public function settings(\Byd\ApiClient\Value\Vin $vin): \Byd\ApiClient\Service\VehicleSettingsService
```

### `ota`

Return OTA operations for a vehicle VIN.

```php
public function ota(\Byd\ApiClient\Value\Vin $vin): \Byd\ApiClient\Service\OtaService
```

