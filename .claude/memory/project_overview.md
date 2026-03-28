---
name: BYD PHP Client — project overview
description: Architecture, key patterns, bugs fixed, and test setup for the byd-php-client library
type: project
---

PHP 8.1+ client for the BYD vehicle API. Main public API is `Client.php`; all request logic lives in `src/Api/`; response models in `src/Models/`.

**Why:** Reverse-engineered from pyBYD; uses Bangcle white-box AES outer encryption + standard AES inner payload encryption.

**How to apply:** Use this for context when adding new endpoints or debugging API responses.

## Enum pattern
All state fields use PHP 8.1 native backed `int` enums (NOT the old `AbstractEnum` class-constants). Every enum has `UNKNOWN = -1` as sentinel so `tryFrom($val) ?? Enum::UNKNOWN` always returns a typed value. The `AbstractEnum` base class is kept for backward-compat but no longer used by any concrete class.

## Bugs fixed in 2026-03-28 session
- `BydApiException` extended `Exception` instead of `BydException` — hierarchy was broken
- `TokenJson::postTokenJson()` checked error code on `$payload` (the hex string) instead of `$decoded` (outer response JSON). Also crashed when `respondData` was null.
- `VehicleRealtimeData` had a broken alias `'recent50kmEnergy' => 'recent50KmEnergy'` that renamed the key but then read from the original (now missing) key.
- No sentinel value normalization: `-129` for `tempInCar`, `-1` for `fullHour`/`fullMinute`/`remainingHours`/`remainingMinutes`/`oilEndurance`/`oilPercent`/`ectValue` were stored as raw values; now → `null`.

## Tests
140 unit tests (460 assertions) in `tests/Models/` — no network access required.
Run via `docker compose run --rm php vendor/bin/phpunit`.
