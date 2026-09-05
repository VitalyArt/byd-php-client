# Watch QR authorization

The generated watch IMEI identifies the watch to the backend. Persist it with the rest of the watch configuration.

```php
$watch->synchronizeServerTime();
$session = $watch->createQrSession();

// The application should render this value as a QR code.
$qrPayload = $session->qrPayload;

try {
    $token = $watch->authorize($session);
    $configuration = $watch->vehicle($token->token);
} catch (\Byd\ApiClient\Exception\WatchAuthorizationException $exception) {
    echo 'Authorization ended as ' . $exception->status->name;
}
```

For non-blocking integrations, replace `authorize()` with repeated `checkQrSession($session)` calls. Stop polling when the status is `APPROVED`, `INVALIDATED` or `EXPIRED`.
