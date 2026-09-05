# `Byd\ApiClient\Service\NotificationService`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/Service/NotificationService.php`

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Value\Vin $vin, \Byd\ApiClient\ProtocolClient $protocol, \Byd\ApiClient\Serialization\DtoSerializer $serializer): mixed
```

### `state`

Fetch push-notification switch states.

```php
public function state(): \Byd\ApiClient\Dto\Response\PushNotificationState
```

### `setEnabled`

Enable or disable vehicle-status push notifications.

```php
public function setEnabled(bool $enabled): \Byd\ApiClient\Dto\Response\CommandResult
```

