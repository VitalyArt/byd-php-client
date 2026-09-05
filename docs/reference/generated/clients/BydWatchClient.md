# `Byd\ApiClient\BydWatchClient`

_No PHPDoc description provided._

**Type:** `class`  
**Source:** `src/BydWatchClient.php`

## Public methods

### `__construct`

_No PHPDoc description provided._

```php
public function __construct(\Byd\ApiClient\Config\WatchClientConfig $config, ?\Psr\Http\Client\ClientInterface $httpClient = null, (Psr\Http\Message\RequestFactoryInterface&Psr\Http\Message\StreamFactoryInterface)|null $httpFactory = null, ?\Psr\Log\LoggerInterface $logger = null, ?\Psr\Clock\ClockInterface $clock = null, ?\Byd\ApiClient\Contract\SleeperInterface $sleeper = null, ?\Byd\ApiClient\Contract\NonceGeneratorInterface $nonceGenerator = null, ?\Byd\ApiClient\Contract\WatchTransportInterface $transport = null, ?\Byd\ApiClient\Serialization\DtoSerializer $serializer = null): mixed
```

### `synchronizeServerTime`

Synchronize the watch protocol clock with the BYD server.

```php
public function synchronizeServerTime(): \Byd\ApiClient\Dto\Response\WatchServerTime
```

### `createQrSession`

Create a QR authorization session for the watch.

```php
public function createQrSession(): \Byd\ApiClient\Dto\Response\WatchQrSession
```

### `checkQrSession`

Check the current status of a QR authorization session.

```php
public function checkQrSession(\Byd\ApiClient\Dto\Response\WatchQrSession $session): \Byd\ApiClient\Dto\Response\WatchQrStatusResponse
```

### `waitForAuthorization`

Poll a QR session until it reaches a terminal status.

```php
public function waitForAuthorization(\Byd\ApiClient\Dto\Response\WatchQrSession $session): \Byd\ApiClient\Dto\Response\WatchQrStatusResponse
```

### `authorize`

Poll a QR session and exchange an approved session for a watch token.

```php
public function authorize(\Byd\ApiClient\Dto\Response\WatchQrSession $session): \Byd\ApiClient\Dto\Response\WatchTokenResponse
```

### `gainToken`

Exchange an approved QR session for a watch token without polling.

```php
public function gainToken(\Byd\ApiClient\Dto\Response\WatchQrSession $session): \Byd\ApiClient\Dto\Response\WatchTokenResponse
```

### `vehicle`

Retrieve the vehicle configuration associated with a watch token.

```php
public function vehicle(\Byd\ApiClient\Dto\Response\WatchTokenInfo $token): \Byd\ApiClient\Dto\Response\WatchVehicleConfiguration
```

### `bluetooth`

Retrieve Bluetooth digital-key information for a watch token.

```php
public function bluetooth(\Byd\ApiClient\Dto\Response\WatchTokenInfo $token): \Byd\ApiClient\Dto\Response\WatchBluetoothInfo
```

### `logout`

Invalidate the watch token at the BYD backend.

```php
public function logout(\Byd\ApiClient\Dto\Response\WatchTokenInfo $token): void
```

