# Vehicle API

`BydClient` exposes one service per capability. All vehicle services are selected with a `Vin` value object.

```php
use Byd\ApiClient\Value\Vin;

$vehicle = $client->vehicles()->all()[0];
$vin = $vehicle->vin;

$telemetry = $client->telemetry($vin)->realtime();
$position = $client->telemetry($vin)->gps();
$climate = $client->climate($vin)->status();
$charging = $client->charging($vin)->status();
```

## Service overview

| Service | Main methods |
| --- | --- |
| `VehicleService` | `all()`, `get()` |
| `TelemetryService` | `realtime()`, `gps()`, `energyConsumption()` |
| `ClimateService` | `status()`, `start()`, `stop()`, `schedule()` |
| `ChargingService` | `status()`, `schedule()`, `saveSchedule()`, `setSmartCharging()`, `start()` |
| `ControlService` | locks, windows, trunk, lights, climate, seats and battery heat |
| `NotificationService` | `state()`, `setEnabled()` |
| `VehicleSettingsService` | `rename()` |
| `OtaService` | `status()`, `book()`, `cancelBooking()`, `start()` |

See the generated [service reference](../reference/generated/services/index.md) for exact signatures and return types.

!!! warning
    Control, charging, climate, rename and OTA methods can change vehicle state. Verify the VIN and vehicle conditions before calling them.
