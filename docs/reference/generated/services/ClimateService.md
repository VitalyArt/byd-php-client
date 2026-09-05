# `Byd\ApiClient\Service\ClimateService`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Service/ClimateService.php`

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, \Byd\ApiClient\ProtocolClient $protocol, \Byd\ApiClient\Serialization\DtoSerializer $serializer, \Byd\ApiClient\Service\ControlService $controls): mixed
```

### `status`

Fetch the current climate status.

```php
public function status(): \Byd\ApiClient\Dto\Response\ClimateStatus
```

### `start`

Start climate control using the requested settings.

```php
public function start(\Byd\ApiClient\Dto\Request\ClimateStartRequest $request): \Byd\ApiClient\Dto\Response\CommandResult
```

### `stop`

Stop climate control.

```php
public function stop(): \Byd\ApiClient\Dto\Response\CommandResult
```

### `schedule`

Schedule a climate-control window.

```php
public function schedule(\Byd\ApiClient\Dto\Request\ClimateScheduleCommand $request): \Byd\ApiClient\Dto\Response\CommandResult
```

