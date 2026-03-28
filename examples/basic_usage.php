<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Byd\ApiClient\Client;
use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Exceptions\BydException;
use Byd\ApiClient\Models\ChargingState;
use Byd\ApiClient\Models\OnlineState;

try {
    $config = new BydConfig(
        getenv('BYD_USERNAME') ?: 'test@example.com',
        getenv('BYD_PASSWORD') ?: 'password123',
        getenv('BYD_BASE_URL') ?: 'https://dilinkappoversea-eu.byd.auto',
        getenv('BYD_COUNTRY_CODE') ?: 'NL',
    );

    $client = new Client($config);

    echo "Logging in...\n";
    $client->login();
    echo "Login successful!\n\n";

    echo "Fetching vehicles...\n";
    $vehicles = $client->getVehicles();

    foreach ($vehicles as $vehicle) {
        $vin = $vehicle->getVin();
        echo "=== Vehicle: {$vehicle->getModelName()} ({$vin}) ===\n";
        echo "Brand: {$vehicle->getBrandName()}\n";
        echo "Plate: {$vehicle->getAutoPlate()}\n\n";

        // Realtime data
        echo "--- Realtime Data ---\n";
        $realtime = $client->getVehicleRealtime($vin);
        echo "Online: " . $realtime->getOnlineState()->name . "\n";
        echo "Vehicle state: " . $realtime->getVehicleState()->name . "\n";
        echo "Battery: " . $realtime->getElecPercent() . "%\n";
        echo "Range: " . $realtime->getEnduranceMileageV2() . " " . $realtime->getEnduranceMileageV2Unit() . "\n";
        echo "Total mileage: " . $realtime->getTotalMileageV2() . " " . $realtime->getTotalMileageV2Unit() . "\n";
        echo "Charge state: " . $realtime->getChargeState()->name . "\n";
        echo "Locked: " . ($realtime->isLocked() ? 'yes' : ($realtime->isLocked() === null ? 'unknown' : 'no')) . "\n";
        echo "Doors open: " . ($realtime->isAnyDoorOpen() ? 'yes' : 'no') . "\n";
        echo "Windows open: " . ($realtime->isAnyWindowOpen() ? 'yes' : 'no') . "\n";
        echo "Tire pressure unit: " . $realtime->getTirePressUnit()->name . "\n";
        if ($realtime->isInteriorTempAvailable()) {
            echo "Interior temp: " . $realtime->getTempInCar() . "°C\n";
        }
        echo "\n";

        // HVAC
        echo "--- HVAC Status ---\n";
        try {
            $hvac = $client->getHvacStatus($vin);

            echo "HVAC status: " . $hvac->getStatus()->name . "\n";
            echo "AC mode: " . $hvac->getAirConditioningMode()->name . "\n";
            echo "Wind mode: " . $hvac->getWindMode()->name . "\n";
            echo "Driver seat heat: " . $hvac->getMainSeatHeatState()->name . "\n";
            echo "Steering wheel heat: " . $hvac->getSteeringWheelHeatState()->name . "\n";
            if ($hvac->getTempInCar() !== null) {
                echo "Interior temp: " . $hvac->getTempInCar() . "°C\n";
            }
        } catch (BydException $e) {
            echo "HVAC error: " . $e->getMessage() . "\n";
        }
        echo "\n";

        // GPS
        echo "--- GPS Info ---\n";
        try {
            $gps = $client->getGpsInfo($vin);
            if ($gps->getLatitude() !== null) {
                echo "Location: " . $gps->getLatitude() . ", " . $gps->getLongitude() . "\n";
                echo "Speed: " . $gps->getSpeed() . " km/h\n";
            } else {
                echo "GPS: no data available\n";
            }
        } catch (BydException $e) {
            echo "GPS error: " . $e->getMessage() . "\n";
        }
        echo "\n";

        // Charging
        echo "--- Charging Status ---\n";
        try {
            $charging = $client->getChargingStatus($vin);
            echo "Charging state: " . $charging->getChargingState()->name . "\n";
        } catch (BydException $e) {
            echo "Charging error: " . $e->getMessage() . "\n";
        }
        echo "\n";

        // Energy
        echo "--- Energy Consumption ---\n";
        try {
            $energy = $client->getEnergyConsumption($vin);
        } catch (BydException $e) {
            echo "Energy error: " . $e->getMessage() . "\n";
        }
        echo "\n";
    }

} catch (BydException $e) {
    echo "BYD API Error: " . $e->getMessage() . "\n";
    if ($e->getCode()) {
        echo "Error Code: " . $e->getCode() . "\n";
    }
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
