# `Byd\ApiClient\Service\VehicleService`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Service/VehicleService.php`

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\ProtocolClient $protocol, \Byd\ApiClient\Serialization\DtoSerializer $serializer): mixed
```

### `all`

Return all vehicles associated with the authenticated account.

```php
public function all(): array
```

### `get`

Find a vehicle by VIN, loading the account list if necessary.

```php
public function get(\Byd\ApiClient\Value\Vin $vin): ?\Byd\ApiClient\Dto\Response\Vehicle
```

