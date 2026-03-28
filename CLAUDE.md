# CLAUDE.md — BYD PHP Client

## Project overview

This is a PHP 8.1+ client library for the BYD vehicle API. It provides classes to authenticate, fetch vehicle telemetry (realtime data, HVAC status, GPS, charging status, energy consumption), and send remote control commands.

## Architecture

```
src/
  Api/          — One class per API endpoint group (Login, VehicleApi, RealtimeApi, HvacApi, GpsApi, ChargingApi, EnergyApi, ControlApi, …)
  Config/       — BydConfig (credentials + base URL), DeviceProfile (app fingerprint)
  Crypto/       — AES/PKCS7 encryption, Bangcle SDK codec, request signing
  Exceptions/   — BydException hierarchy
  Models/       — Response models and PHP 8.1 native backed enums
  Transport/    — SecureTransport (Guzzle-based HTTPS client)
  Client.php    — Public facade; all user-facing methods live here
  Session.php   — Holds auth token + VIN-level state after login
tests/
  Models/       — Unit tests for model classes and enums (no network required)
```

## Enums

All state/flag fields use **PHP 8.1 native backed `int` enums** (not the old `AbstractEnum` class-constants pattern). Every enum has a sentinel `UNKNOWN = -1` case so that `tryFrom()` with a default of `?? EnumType::UNKNOWN` always returns a typed value.

Key enums and their semantic ranges:

| Enum | Values |
|------|--------|
| `OnlineState` | UNKNOWN=-1, ONLINE=1, OFFLINE=2 |
| `ConnectState` | UNKNOWN=-1, DISCONNECTED=0, CONNECTED=1 |
| `VehicleState` | UNKNOWN=-1, OFF=0, ON=2 |
| `ChargingState` | UNKNOWN=-1, NOT_CHARGING=0, CHARGING=1, CONNECTED=15 |
| `PowerGear` | UNKNOWN=-1, OFF=1, ON=3 |
| `LockState` | UNKNOWN=-1, UNAVAILABLE=0, UNLOCKED=1, LOCKED=2 |
| `DoorOpenState` | UNKNOWN=-1, CLOSED=0, OPEN=1 |
| `WindowState` | UNKNOWN=-1, CLOSED=1, OPEN=2 |
| `AirCirculationMode` | UNKNOWN=-1, UNAVAILABLE=0, EXTERNAL=1, INTERNAL=2 |
| `SeatHeatVentState` | UNKNOWN=-1, NO_DATA=0, OFF=1, LOW=2, HIGH=3 |
| `StearingWheelHeat` | ON=-1, OFF=1 (note: live-confirmed, ON really is -1) |
| `TirePressureUnit` | UNKNOWN=-1, BAR=1, PSI=2, KPA=3 |
| `AcSwitch` | UNKNOWN=-1, OFF=0, ON=1, HEAT=2 |
| `AirConditioningMode` | UNKNOWN=-1, OFF=0, AUTO=1, MANUAL=2 |
| `HvacOverallStatus` | UNKNOWN=-1, ON=1, OFF=2 |
| `HvacWindMode` | UNKNOWN=-1, OFF=0, FACE=1, FACE_FOOT=2, FOOT=3, FOOT_DEFROST=4, DEFROST=5 |
| `HvacWindPosition` | UNKNOWN=-1, OFF=0, POSITION_1..POSITION_7=1..7 |

`SeatHeatVentState::toCommandLevel()` and `StearingWheelHeat::toCommandLevel()` return the inverted command value for remote control API calls.

## Sentinel values

The BYD API uses several "no data" sentinel values that are normalized to `null` in the model `populate()` methods:

- `tempInCar <= -100.0` → `null` (API sends -129 when sensor has no reading)
- `fullHour < 0`, `fullMinute < 0` → `null` (charging time estimates)
- `remainingHours < 0`, `remainingMinutes < 0` → `null`
- `oilEndurance < 0`, `oilPercent < 0` → `null` (hybrid vehicles without oil data)
- `ectValue < 0` → `null`

## Key field aliases

The `VehicleRealtimeData::populate()` method normalises these inconsistent API field names before processing:

| API field (raw) | Model property |
|----------------|----------------|
| `backCover` | `trunkLid` |
| `leftFrontTirepressure` | `leftFrontTirePressure` |
| `rightFrontTirepressure` | `rightFrontTirePressure` |
| `leftRearTirepressure` | `leftRearTirePressure` |
| `rightRearTirepressure` | `rightRearTirePressure` |
| `abs` | `absWarning` |
| `time` | `timestamp` |
| `stearingWheelHeatState` (typo) | `steeringWheelHeatState` |

Note: `recent50kmEnergy` is stored as-is (previous alias `recent50KmEnergy` was a bug — the key never existed in the API response after the rename, so the value was always `null`).

## Response nesting by endpoint

Different endpoints wrap their payload under different keys. `HvacApi` and `GpsApi` extract the sub-key before passing data to the model:

| Endpoint | Wrapper key | Consumer |
|----------|-------------|----------|
| `/control/getStatusNow` | `statusNow` | `HvacApi` → `HvacStatus` |
| `/control/getGpsInfo` | `data` | `GpsApi` → `GpsInfo` reads `$data['data'][...]` |

A flat array passed directly to `HvacStatus` or `GpsInfo` will produce all-null / UNKNOWN getters.

## Running tests

```bash
composer test
# or directly:
php vendor/bin/phpunit
```

Test classes live in `tests/Models/` and require no network access or API credentials.

## Running static analysis

```bash
vendor/bin/phpstan analyse
```

## Code style

```bash
composer cs-fix
# or directly:
php vendor/bin/php-cs-fixer fix
```

## Adding a new endpoint

1. Create an API class in `src/Api/` that extends or composes `Common`.
2. Create a response model in `src/Models/` extending `BaseModel` with a `populate(array $data)` method.
3. Add typed enum properties where the API returns numeric state codes.
4. Expose a public method on `Client.php`.
5. Write unit tests in `tests/Models/` covering defaults, normal population, and any sentinel normalisation.

## Dependencies

- PHP ^8.1 (native enums, union types, `match` expressions)
- `guzzlehttp/guzzle` ^7.0 — HTTP transport
- `psr/log` ^3.0 — optional logging

Dev:
- `phpunit/phpunit` ^10.0
- `friendsofphp/php-cs-fixer` ^3.0
- `phpstan/phpstan` ^2.1
