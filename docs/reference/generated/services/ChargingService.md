# `Byd\ApiClient\Service\ChargingService`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Service/ChargingService.php`

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, \Byd\ApiClient\ProtocolClient $protocol, \Byd\ApiClient\Serialization\DtoSerializer $serializer, \Byd\ApiClient\PollingExecutor $polling): mixed
```

### `status`

Fetch the current charging status.

```php
public function status(): \Byd\ApiClient\Dto\Response\ChargingStatus
```

### `schedule`

Fetch the configured smart-charging schedule.

```php
public function schedule(): \Byd\ApiClient\Dto\Response\ChargingSchedule
```

### `saveSchedule`

Save or update the charging schedule.

```php
public function saveSchedule(\Byd\ApiClient\Dto\Request\ChargingScheduleRequest $request): \Byd\ApiClient\Dto\Response\CommandResult
```

### `setSmartCharging`

Enable or disable smart charging.

```php
public function setSmartCharging(bool $enabled): \Byd\ApiClient\Dto\Response\CommandResult
```

### `start`

Start charging and wait for a terminal command result.

```php
public function start(): \Byd\ApiClient\Dto\Response\CommandResult
```

