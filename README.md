# BYD API Client for PHP

Modern, typed and synchronous client for the BYD vehicle API. Requires PHP 8.4+ and uses immutable DTOs, Symfony Serializer attributes, PSR-18 HTTP transport and PSR-3 logging.

> BYD does not publish this API. Protocol changes may happen without notice.

## Installation

```bash
composer require byd/api-client
```

## Usage

```php
use Byd\ApiClient\BydClient;
use Byd\ApiClient\Config\ClientConfig;
use Byd\ApiClient\Config\Credentials;

$client = new BydClient(new ClientConfig(
    credentials: new Credentials(
        username: 'name@example.com',
        password: 'secret',
        controlPin: '1234',
    ),
));

$vehicle = $client->vehicles()->all()[0];

$telemetry = $client->telemetry($vehicle->vin)->realtime();
$position = $client->telemetry($vehicle->vin)->gps();
$climate = $client->climate($vehicle->vin)->status();
$charging = $client->charging($vehicle->vin)->status();
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

Request DTO constructors validate invariants before a network request. These include `ClimateStartRequest`, `ClimateScheduleCommand`, `ChargingScheduleRequest`, `SeatClimateRequest` and `BatteryHeatRequest`.

## Configuration and dependency injection

`ClientConfig` groups credentials, locale, device profile, protocol settings and retry/polling policies. `EnvironmentConfigLoader` is the only environment-input adapter.

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
