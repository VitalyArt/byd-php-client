# Installation and configuration

## Requirements

- PHP 8.4 or newer
- Composer
- JSON and HTTPS support

Install the package:

```bash
composer require byd/api-client
```

## Construct a client

```php
use Byd\ApiClient\BydClient;
use Byd\ApiClient\Enum\CountryCode;

$client = new BydClient(
    username: $_ENV['BYD_USERNAME'],
    password: $_ENV['BYD_PASSWORD'],
    countryCode: CountryCode::UZ,
    language: 'en',
    timeZone: 'Asia/Tashkent',
    controlPin: $_ENV['BYD_CONTROL_PIN'] ?? null,
);
```

The regional endpoint is selected from `CountryCode`. The client authenticates lazily when the first API request is made; call `authenticate()` explicitly when you want to fail fast.

## Environment configuration

`EnvironmentConfigLoader` accepts `BYD_USERNAME` and `BYD_PASSWORD`. Optional variables are `BYD_CONTROL_PIN`, `BYD_COUNTRY_CODE`, `BYD_LANGUAGE` and `BYD_TIME_ZONE`.

```php
use Byd\ApiClient\Config\EnvironmentConfigLoader;

$client = (new EnvironmentConfigLoader())->load([
    'BYD_USERNAME' => getenv('BYD_USERNAME'),
    'BYD_PASSWORD' => getenv('BYD_PASSWORD'),
    'BYD_COUNTRY_CODE' => getenv('BYD_COUNTRY_CODE') ?: 'UZ',
    'BYD_LANGUAGE' => getenv('BYD_LANGUAGE') ?: 'en',
    'BYD_TIME_ZONE' => getenv('BYD_TIME_ZONE') ?: 'Asia/Tashkent',
]);
```

Keep credentials and control PIN outside source control. Do not log tokens, PINs, watch QR payloads or Bluetooth digital keys.

## Local documentation preview

```bash
make docs-serve
```

Alternatively, install the documentation tools manually with `python3 -m venv .venv-docs` and `pip install -r docs/requirements.txt`.
