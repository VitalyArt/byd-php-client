# BYD API Client (PHP)

PHP client library for the BYD vehicle API.

## Features

- [x] Authentication
- [x] Vehicle listing
- [x] Real-time data
- [x] GPS information
- [x] HVAC status
- [x] Charging status
- [x] Energy consumption
- [x] Remote control
- [x] Smart charging
- [x] Push notifications
- [x] Vehicle settings

## Installation

```bash
composer require byd/api-client
```

## Usage

```php
<?php

require_once 'vendor/autoload.php';

use Byd\ApiClient\Client;
use Byd\ApiClient\Config\BydConfig;

// Create configuration
$config = BydConfig::fromEnv([
    'username' => 'your-email@example.com',
    'password' => 'your-password',
    // ... other options
]);

// Create client
$client = new Client($config);

// Login
$client->login();

// Get vehicles
$vehicles = $client->getVehicles();

foreach ($vehicles as $vehicle) {
    echo "VIN: " . $vehicle->getVin() . "\n";
    echo "Model: " . $vehicle->getModelName() . "\n";
    echo "---\n";
}
```

## Configuration

The client can be configured in several ways:

### Environment Variables

```env
BYD_USERNAME=your-email@example.com
BYD_PASSWORD=your-password
BYD_BASE_URL=https://dilinkappoversea-eu.byd.auto
BYD_COUNTRY_CODE=NL
BYD_LANGUAGE=en
BYD_TIME_ZONE=Europe/Amsterdam
```

### Direct Configuration

```php
$config = new BydConfig(
    'your-email@example.com',
    'your-password',
    'https://dilinkappoversea-eu.byd.auto', // base URL
    'NL', // country code
    'en', // language
    'Europe/Amsterdam', // time zone
    // ... other options
);
```

## API Documentation

Key classes and methods:

- [`Client`](src/Client.php) - Main client class
- [`BydConfig`](src/Config/BydConfig.php) - Configuration class
- [`Vehicle`](src/Models/Vehicle.php) - Vehicle model
- [`VehicleApi`](src/Api/VehicleApi.php) - Vehicle-related API methods
- [`RealtimeApi`](src/Api/RealtimeApi.php) - Real-time data API methods
- [`GpsApi`](src/Api/GpsApi.php) - GPS information API methods
- [`HvacApi`](src/Api/HvacApi.php) - HVAC status API methods
- [`ChargingApi`](src/Api/ChargingApi.php) - Charging status API methods
- [`EnergyApi`](src/Api/EnergyApi.php) - Energy consumption API methods
- [`ControlApi`](src/Api/ControlApi.php) - Remote control API methods
- [`SmartChargingApi`](src/Api/SmartChargingApi.php) - Smart charging API methods
- [`PushNotificationsApi`](src/Api/PushNotificationsApi.php) - Push notifications API methods
- [`VehicleSettingsApi`](src/Api/VehicleSettingsApi.php) - Vehicle settings API methods

## Requirements

- PHP 8.1+
- ext-json
- guzzlehttp/guzzle ^7.0
- psr/log ^3.0

## Development

### Install dependencies

```bash
composer install
```

### Run tests

```bash
composer test
```

### Code style

```bash
composer cs-fix
```

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.