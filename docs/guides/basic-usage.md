# Basic client usage

```php
use Byd\ApiClient\BydClient;
use Byd\ApiClient\Enum\CountryCode;
use Byd\ApiClient\Exception\BydException;

try {
    $client = new BydClient(
        username: 'name@example.com',
        password: 'secret',
        countryCode: CountryCode::UZ,
        language: 'en',
        timeZone: 'Asia/Tashkent',
    );

    $client->authenticate();
    foreach ($client->vehicles()->all() as $vehicle) {
        echo $vehicle->vin->value . ': ' . $vehicle->modelName . PHP_EOL;
        $data = $client->telemetry($vehicle->vin)->realtime();
        echo 'Battery: ' . ($data->stateOfCharge ?? 'unknown') . "%\n";
    }
} catch (BydException $exception) {
    // Handle API, transport, protocol and authentication failures.
    error_log($exception->getMessage());
}
```

Response objects are immutable, typed DTOs. Unknown response fields are retained in `$dto->raw`, and unknown enum values fall back to the enum's `UNKNOWN` case where supported.
