# BYD API Client for PHP

Modern, typed and synchronous client for the BYD vehicle API. Requires PHP 8.4+ and uses immutable DTOs, Symfony Serializer attributes, PSR-18 HTTP transport and PSR-3 logging.

Read the full [HTML documentation](https://vitalyart.github.io/byd-php-client/) for installation, practical guides, API overviews and the generated [Reference](https://vitalyart.github.io/byd-php-client/reference/).

To preview the documentation locally, install the tools from `docs/requirements.txt`, run `php tools/generate-reference.php`, then run `mkdocs serve`.

> BYD does not publish this API. Protocol changes may happen without notice.

## Installation

```bash
composer require byd/api-client
```

## Usage

```php
use Byd\ApiClient\BydClient;
use Byd\ApiClient\Enum\CountryCode;

$client = new BydClient(
    username: 'name@example.com',
    password: 'secret',
    countryCode: CountryCode::UZ,
    controlPin: '1234',
);

$vehicle = $client->vehicles()->all()[0];

$telemetry = $client->telemetry($vehicle->vin)->realtime();
$position = $client->telemetry($vehicle->vin)->gps();
$climate = $client->climate($vehicle->vin)->status();
$charging = $client->charging($vehicle->vin)->status();
$ota = $client->ota($vehicle->vin)->status();
$client->controls($vehicle->vin)->lock();
```

All response objects are immutable `readonly` DTOs with public typed properties. The original server response is available through `$dto->raw`; unknown fields do not break deserialization.

## Services

- `$client->vehicles()` — list and resolve account vehicles.
- `$client->telemetry($vin)` — realtime data, GPS and energy consumption.
- `$client->climate($vin)` — status, start, stop and scheduling.
- `$client->charging($vin)` — status, schedule, smart charging and start charging.
- `$client->controls($vin)` — PIN verification, locks, lights, windows, trunk, seats and battery heating.
- `$client->notifications($vin)` — read and change push state.
- `$client->settings($vin)` — rename a vehicle.
- `$client->ota($vin)` — check the installed and available vehicle OTA versions.

## Wear OS authorization

The package also exposes the independently authenticated watch protocol reconstructed from BYD AUTO for Wear OS 1.2.0. Persist the generated pseudo IMEI: changing it makes the backend see a different watch.

```php
use Byd\ApiClient\BydWatchClient;
use Byd\ApiClient\Config\Locale;
use Byd\ApiClient\Config\WatchClientConfig;
use Byd\ApiClient\Config\WatchDeviceProfile;
use Byd\ApiClient\Enum\CountryCode;

$device = WatchDeviceProfile::generate('SAMSUNG', 'SM-R890');
// Persist $device->watchImei and pass it to WatchDeviceProfile next time.

$watch = new BydWatchClient(new WatchClientConfig(
    device: $device,
    locale: new Locale(CountryCode::UZ, 'ru', 'Asia/Tashkent'),
));

$watch->synchronizeServerTime();
$login = $watch->createQrSession();
// Render $login->qrPayload as a QR code and scan it in the mobile BYD app.
$token = $watch->authorize($login); // Polls every 3 s, for at most 150 s.
$vehicle = $watch->vehicle($token->token);
```

For event-loop or UI integrations, call `checkQrSession()` yourself instead of the synchronous `authorize()` helper. The QR states are `WAITING_FOR_SCAN`, `WAITING_FOR_CONFIRMATION`, `APPROVED`, `INVALIDATED`, and `EXPIRED`. Watch tokens, the control password, and Bluetooth `dkey` are secrets and must not be logged.

Request DTO constructors validate invariants before a network request. These include `ClimateStartRequest`, `ClimateScheduleCommand`, `ChargingScheduleRequest`, `SeatClimateRequest` and `BatteryHeatRequest`.

## Configuration and dependency injection

`ClientConfig` groups credentials, locale, device profile, protocol settings and retry/polling policies. You can create a client directly without environment variables; the endpoint is derived from `CountryCode`.

For accounts outside the European node, select the API host using BYD's country-to-node table:

```php
use Byd\ApiClient\Enum\CountryCode;

$client = new BydClient(
    username: 'name@example.com',
    password: 'secret',
    countryCode: CountryCode::TH,
    language: 'en',
    timeZone: 'Asia/Bangkok',
);
```

`EnvironmentConfigLoader` creates a `BydClient` from `BYD_USERNAME`, `BYD_PASSWORD`, optional `BYD_CONTROL_PIN`, `BYD_COUNTRY_CODE`, `BYD_LANGUAGE` and `BYD_TIME_ZONE`; `BYD_BASE_URL` is not used. The resolver deliberately maps countries through BYD's regional nodes: for example Thailand uses the Singapore endpoint, while Norway uses the European endpoint.

Guzzle is used by default. A custom PSR-18 client, PSR-17 request/stream factory, logger, clock, sleeper, nonce generator, secure transport or DTO serializer can be injected through `BydClient`.

Expired sessions follow `AuthenticationRetryPolicy`. Only an explicit session-expired response is retried; ambiguous transport failures are never replayed. Polling behavior is controlled by a shared `PollingPolicy` and testable clock/sleeper abstractions.

## Serialization

Every request, response and protocol envelope is an object under `Dto`. Every JSON property has an explicit Symfony `#[SerializedName]`; diagnostic fields use `#[Ignore]`. `DtoSerializer` centralizes aliases, unknown enum fallback, numeric-string conversion and sentinel normalization.

To add an endpoint, add an `Endpoint` enum case, immutable request/response DTOs, and a method in the appropriate resource service. Do not build associative request arrays inside services.

## Quality checks

```bash
composer test
composer phpstan
composer cs-check
composer rector-check
composer validate --strict
composer audit
```

Live integration tests are opt-in and require real BYD credentials.
