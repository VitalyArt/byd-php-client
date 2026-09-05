# `Byd\ApiClient\Service\ControlService`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Service/ControlService.php`

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, \Byd\ApiClient\Config\ClientConfig $config, \Byd\ApiClient\ProtocolClient $protocol, \Byd\ApiClient\Serialization\DtoSerializer $serializer, \Byd\ApiClient\Serialization\ProtocolPayloadNormalizer $payloadNormalizer, \Byd\ApiClient\PollingExecutor $polling, \Byd\ApiClient\Crypto\Cryptography $cryptography): mixed
```

### `verifyPin`

Verify the configured or supplied control PIN.

```php
public function verifyPin(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `execute`

Execute a remote command and wait for its terminal result.

```php
public function execute(\Byd\ApiClient\Enum\RemoteCommand $command, ?array $parameters = null, ?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `lock`

_No PHPDoc description provided._

```php
public function lock(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `unlock`

_No PHPDoc description provided._

```php
public function unlock(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `flashLights`

_No PHPDoc description provided._

```php
public function flashLights(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `findCar`

_No PHPDoc description provided._

```php
public function findCar(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `openWindows`

_No PHPDoc description provided._

```php
public function openWindows(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `closeWindows`

_No PHPDoc description provided._

```php
public function closeWindows(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `openTrunk`

_No PHPDoc description provided._

```php
public function openTrunk(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `closeTrunk`

_No PHPDoc description provided._

```php
public function closeTrunk(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `setSeatClimate`

_No PHPDoc description provided._

```php
public function setSeatClimate(\Byd\ApiClient\Dto\Request\SeatClimateRequest $request, ?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `setBatteryHeat`

_No PHPDoc description provided._

```php
public function setBatteryHeat(\Byd\ApiClient\Dto\Request\BatteryHeatRequest $request, ?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `executeDto`

_No PHPDoc description provided._

```php
public function executeDto(\Byd\ApiClient\Enum\RemoteCommand $command, object $request, ?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

