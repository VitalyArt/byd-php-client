# `Byd\ApiClient\Service\VehicleSettingsService`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Service/VehicleSettingsService.php`

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, \Byd\ApiClient\ProtocolClient $protocol, \Byd\ApiClient\Serialization\DtoSerializer $serializer): mixed
```

### `rename`

Change the user-visible vehicle alias.

```php
public function rename(string $name): \Byd\ApiClient\Dto\Response\CommandResult
```

