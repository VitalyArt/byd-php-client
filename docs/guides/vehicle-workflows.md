# Vehicle workflows

## Climate control

```php
use Byd\ApiClient\Dto\Request\ClimateStartRequest;

$climate = $client->climate($vehicle->vin);
$status = $climate->status();

if (!$status->isOn()) {
    $climate->start(new ClimateStartRequest(
        temperature: 22.0,
        durationMinutes: 15,
    ));
}
```

## Charging

```php
$charging = $client->charging($vehicle->vin);
$status = $charging->status();

if ($status->state->name === 'NOT_CHARGING') {
    $charging->start();
}

$charging->setSmartCharging(true);
```

## Notifications, rename and OTA

```php
$notifications = $client->notifications($vehicle->vin);
$notifications->setEnabled(true);

$client->settings($vehicle->vin)->rename('Family BYD');

$ota = $client->ota($vehicle->vin);
if ($ota->status()->hasUpdate()) {
    // These operations can change vehicle software and require the control PIN.
    $ota->book();
}
```

Use the generated [request DTO reference](../reference/generated/requests/index.md) for schedule and seat-climate validation rules.
