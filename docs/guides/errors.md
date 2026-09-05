# Errors, sessions and retries

Catch `BydException` around application-level calls and handle more specific subclasses when the application needs different behavior.

```php
use Byd\ApiClient\Exception\ApiException;
use Byd\ApiClient\Exception\BydException;
use Byd\ApiClient\Exception\RemoteControlException;
use Byd\ApiClient\Exception\SessionExpiredException;

try {
    $telemetry = $client->telemetry($vehicle->vin)->realtime();
} catch (SessionExpiredException) {
    $client->authenticate();
    // Retry only when the operation is known to be safe to replay.
} catch (ApiException $exception) {
    error_log(sprintf('BYD API %d at %s', $exception->apiCode, $exception->endpoint->value));
} catch (RemoteControlException $exception) {
    error_log('The remote command did not succeed: '.$exception->getMessage());
} catch (BydException $exception) {
    error_log($exception->getMessage());
}
```

The client retries an explicitly reported expired session according to `AuthenticationRetryPolicy`. Ambiguous transport failures are not replayed automatically. Telemetry, GPS, charging start and remote commands may poll asynchronous BYD results according to `PollingPolicy`.

Request DTO constructors throw `ValidationException` before a network request when values such as temperature, time or state-of-charge limits are invalid.
