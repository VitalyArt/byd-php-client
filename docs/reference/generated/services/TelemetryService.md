# `Byd\ApiClient\Service\TelemetryService`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Service/TelemetryService.php`

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, \Byd\ApiClient\Config\ClientConfig $config, \Byd\ApiClient\Service\VehicleService $vehicles, \Byd\ApiClient\ProtocolClient $protocol, \Byd\ApiClient\Serialization\DtoSerializer $serializer, \Byd\ApiClient\PollingExecutor $polling): mixed
```

### `realtime`

Fetch meaningful realtime telemetry, polling the asynchronous BYD result when needed.

```php
public function realtime(): \Byd\ApiClient\Dto\Response\VehicleTelemetry
```

### `gps`

Fetch a GPS position, polling the asynchronous BYD result when needed.

```php
public function gps(): \Byd\ApiClient\Dto\Response\GpsPosition
```

### `energyConsumption`

Fetch cumulative and recent energy consumption data.

```php
public function energyConsumption(): \Byd\ApiClient\Dto\Response\EnergyConsumption
```

