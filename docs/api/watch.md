# Watch API

`BydWatchClient` uses a separate watch protocol and authentication flow. It does not reuse the account session from `BydClient`.

```php
use Byd\ApiClient\BydWatchClient;
use Byd\ApiClient\Config\Locale;
use Byd\ApiClient\Config\WatchClientConfig;
use Byd\ApiClient\Config\WatchDeviceProfile;
use Byd\ApiClient\Enum\CountryCode;

$device = WatchDeviceProfile::generate('SAMSUNG', 'SM-R890');
// Persist $device->watchImei and reuse it on the next run.
$watch = new BydWatchClient(new WatchClientConfig(
    device: $device,
    locale: new Locale(CountryCode::UZ, 'en', 'Asia/Tashkent'),
));

$watch->synchronizeServerTime();
$session = $watch->createQrSession();
// Render $session->qrPayload as a QR code and scan it in the BYD mobile app.
$token = $watch->authorize($session);
$vehicle = $watch->vehicle($token->token);
$bluetooth = $watch->bluetooth($token->token);
```

`authorize()` polls until the QR session reaches a terminal state. For an event loop or UI, call `checkQrSession()` yourself and handle `WatchQrStatus` values explicitly.

See the generated [Watch client reference](../reference/generated/clients/index.md) and [Watch DTO reference](../reference/generated/responses/index.md).
