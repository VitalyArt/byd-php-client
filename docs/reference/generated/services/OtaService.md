# `Byd\ApiClient\Service\OtaService`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Service/OtaService.php`

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, \Byd\ApiClient\Config\ClientConfig $config, \Byd\ApiClient\ProtocolClient $protocol, \Byd\ApiClient\Serialization\DtoSerializer $serializer, \Byd\ApiClient\Crypto\Cryptography $cryptography): mixed
```

### `status`

Fetch installed and available OTA version information.

```php
public function status(): \Byd\ApiClient\Dto\Response\OtaUpdateInfo
```

### `book`

Book an available OTA update.

```php
public function book(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `cancelBooking`

Cancel a previously booked OTA update.

```php
public function cancelBooking(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

### `start`

Start an OTA update.

```php
public function start(?string $pin = null): \Byd\ApiClient\Dto\Response\CommandResult
```

