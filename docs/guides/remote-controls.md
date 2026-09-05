# Remote controls

Remote commands use the configured control PIN when one is available. A plain PIN is normalized and hashed internally; a pre-hashed 32-character hexadecimal value can also be supplied.

```php
$controls = $client->controls($vehicle->vin);

$result = $controls->lock();
if ($result->isSuccess()) {
    echo "Vehicle locked\n";
}

$controls->flashLights();
$controls->openTrunk();
```

For commands requiring request data:

```php
use Byd\ApiClient\Dto\Request\SeatClimateRequest;
use Byd\ApiClient\Enum\SeatClimateLevel;

$controls->setSeatClimate(new SeatClimateRequest(
    heating: SeatClimateLevel::HIGH,
));
```

!!! warning
    These methods send commands to the vehicle. Treat `RemoteControlException` as a failed command and do not automatically repeat it without checking the vehicle state.
