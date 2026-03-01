<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Byd\ApiClient\Client;
use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Exceptions\BydException;

try {
    // Create configuration
    // You can also use BydConfig::fromEnv() to load from environment variables
    $config = new BydConfig(
        getenv('BYD_USERNAME') ?: 'test@example.com',
        getenv('BYD_PASSWORD') ?: 'password123',
        getenv('BYD_BASE_URL') ?: 'https://dilinkappoversea-eu.byd.auto',
        getenv('BYD_COUNTRY_CODE') ?: 'NL',
    );

    // Create client
    $client = new Client($config);

    // Login
    echo "Logging in...\n";
    $client->login();
    echo "Login successful!\n";

    // Get vehicles
    echo "Fetching vehicles...\n";
    $vehicles = $client->getVehicles();

    foreach ($vehicles as $vehicle) {
        echo "VIN: " . $vehicle->getVin() . "\n";
        echo "Model: " . $vehicle->getModelName() . "\n";
        echo "Brand: " . $vehicle->getBrandName() . "\n";
        echo "---\n";
    }

} catch (BydException $e) {
    echo "BYD API Error: " . $e->getMessage() . "\n";
    if ($e->getCode()) {
        echo "Error Code: " . $e->getCode() . "\n";
    }
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
}
