<?php

declare(strict_types=1);

require dirname(__DIR__).'/vendor/autoload.php';

use Byd\ApiClient\Config\EnvironmentConfigLoader;
use Byd\ApiClient\Enum\EnergyType;
use Byd\ApiClient\Exception\BydException;

/** @param callable(): void $operation */
function section(string $title, callable $operation): void
{
    echo "\n--- {$title} ---\n";

    try {
        $operation();
    } catch (BydException $exception) {
        echo 'Unavailable: '.$exception->getMessage()."\n";
    }
}

function value(int|float|string|null $value, string $unit = ''): string
{
    return $value === null || $value === '' ? 'unknown' : (string) $value.$unit;
}

function optional(string $label, int|float|string|null $value, string $unit = ''): void
{
    if ($value !== null && $value !== '') {
        echo $label.': '.$value.$unit."\n";
    }
}

$environment = [];
foreach (['BYD_USERNAME', 'BYD_PASSWORD', 'BYD_CONTROL_PIN', 'BYD_COUNTRY_CODE', 'BYD_LANGUAGE', 'BYD_TIME_ZONE'] as $name) {
    $environment[$name] = getenv($name);
}

try {
    $client = (new EnvironmentConfigLoader())->load($environment);
    $vehicles = $client->vehicles()->all();

    if ($vehicles === []) {
        echo "No vehicles found.\n";

        exit(0);
    }

    echo 'Vehicles: '.count($vehicles)."\n";

    foreach ($vehicles as $index => $vehicle) {
        $vin = $vehicle->vin;
        $energyType = EnergyType::tryFrom($vehicle->energyType);

        echo "\n============================================================\n";
        echo sprintf("Vehicle #%d: %s %s\n", $index + 1, $vehicle->brandName, $vehicle->modelName);
        echo "============================================================\n";
        echo "VIN: {$vin->value}\n";
        echo 'Alias: '.value($vehicle->alias)."\n";
        echo 'Plate: '.value($vehicle->plate)."\n";
        echo 'Energy type: '.($energyType?->name ?? $vehicle->energyType)."\n";
        echo 'Default vehicle: '.($vehicle->isDefault() ? 'yes' : 'no')."\n";
        echo 'Reported mileage: '.value($vehicle->totalMileage, ' km')."\n";

        section('Realtime telemetry', function () use ($client, $vin): void {
            $telemetry = $client->telemetry($vin)->realtime();

            echo "Online: {$telemetry->onlineState->name}\n";
            echo "Charging: {$telemetry->chargingState->name}\n";
            echo 'Battery: '.value($telemetry->stateOfCharge, '%')."\n";
            echo 'Remaining range: '.value($telemetry->remainingRange, ' km')."\n";
            echo 'Total mileage: '.value($telemetry->totalMileage, ' km')."\n";
            echo 'Speed: '.value($telemetry->speed, ' km/h')."\n";
            echo 'Interior temperature: '.value($telemetry->interiorTemperature, ' °C')."\n";
            optional('Exterior temperature', $telemetry->exteriorTemperature, ' °C');
            echo 'Doors: '.($telemetry->isAnyDoorOpen() ? 'open' : 'closed')."\n";
            echo 'Windows: '.($telemetry->isAnyWindowOpen() ? 'open' : 'closed')."\n";
            echo "Front doors: {$telemetry->leftFrontDoor->name} / {$telemetry->rightFrontDoor->name}\n";
            echo "Rear doors: {$telemetry->leftRearDoor->name} / {$telemetry->rightRearDoor->name}\n";
            echo "Front locks: {$telemetry->leftFrontLock->name} / {$telemetry->rightFrontLock->name}\n";
            echo "Rear locks: {$telemetry->leftRearLock->name} / {$telemetry->rightRearLock->name}\n";
            $tirePressure = [$telemetry->leftFrontTirePressure, $telemetry->rightFrontTirePressure, $telemetry->leftRearTirePressure, $telemetry->rightRearTirePressure];
            if (!in_array(null, $tirePressure, true)) {
                echo 'Tire pressure (FL/FR/RL/RR): '.implode(' / ', $tirePressure)." kPa\n";
            }

            if ($telemetry->hoursToFull !== null || $telemetry->minutesToFull !== null) {
                echo 'Time to full: '.value($telemetry->hoursToFull, ' h').' '.value($telemetry->minutesToFull, ' min')."\n";
            }
        });

        section('GPS position', function () use ($client, $vin): void {
            $position = $client->telemetry($vin)->gps();

            echo 'Coordinates: '.value($position->latitude).' / '.value($position->longitude)."\n";
            optional('Altitude', $position->altitude, ' m');
            optional('Speed', $position->speed, ' km/h');
            optional('Heading', $position->heading ?? $position->direction, '°');
            optional('Position type', $position->positionType);
        });

        section('Climate', function () use ($client, $vin): void {
            $climate = $client->climate($vin)->status();

            echo 'Power: '.($climate->isOn() ? 'on' : 'off')."\n";
            echo "Mode: {$climate->mode->name}\n";
            echo 'Interior temperature: '.value($climate->interiorTemperature, ' °C')."\n";
            echo 'Exterior temperature: '.value($climate->exteriorTemperature, ' °C')."\n";
            echo 'Driver target: '.value($climate->driverTemperature, ' °C')."\n";
            echo 'Passenger target: '.value($climate->passengerTemperature, ' °C')."\n";
            echo "Driver seat heat/ventilation: {$climate->driverSeatHeat->name} / {$climate->driverSeatVentilation->name}\n";
            echo "Passenger seat heat/ventilation: {$climate->passengerSeatHeat->name} / {$climate->passengerSeatVentilation->name}\n";
            echo 'Particulate matter: '.value($climate->particulateMatter)."\n";
        });

        section('Charging', function () use ($client, $vin): void {
            $charging = $client->charging($vin);
            $status = $charging->status();
            $schedule = $charging->schedule();

            echo "State: {$status->state->name}\n";
            echo 'Battery: '.value($status->stateOfCharge, '%')."\n";
            optional('Power', $status->chargingPower ?? $status->chargerPower, ' kW');
            optional('Voltage', $status->chargerVoltage, ' V');
            optional('Current', $status->chargerCurrent, ' A');
            optional('Battery temperature', $status->batteryTemperature, ' °C');
            echo "Connection: {$status->connectionState->name}\n";
            if ($status->hoursToFull !== null || $status->minutesToFull !== null) {
                echo 'Time to full: '.value($status->hoursToFull, ' h').' '.value($status->minutesToFull, ' min')."\n";
            }
            echo 'Target SOC: '.value($status->electricSocLimit ?? $status->hybridSocLimit, '%')."\n";

            $scheduleState = $schedule->isEnabled();
            echo 'Smart charging: '.($scheduleState === null ? 'unknown' : ($scheduleState ? 'enabled' : 'disabled'))."\n";
            echo 'Schedule: '.value($schedule->startTime).'–'.value($schedule->endTime)."\n";
            echo 'Charge mode: '.value($schedule->chargeWay)."\n";
        });

        section('Energy consumption', function () use ($client, $vin): void {
            $energy = $client->telemetry($vin)->energyConsumption();

            $cumulative = $energy->cumulative;
            $recent = $energy->recent;
            $totalMileage = $cumulative?->totalMileage ?? $energy->totalMileage;
            $mileageUnit = $cumulative?->mileageUnit ?? $energy->mileageUnit;
            $cumulativeAverage = $cumulative?->averageElectricConsumption ?? $energy->cumulativeAverageEvConsumption;
            $cumulativeUnit = $cumulative?->electricConsumptionUnit ?? $energy->cumulativeEvUnit;
            $recentAverage = $recent?->averageElectricConsumption ?? $energy->recentAverage;
            $recentAverageUnit = $recent?->averageElectricConsumptionUnit;
            $recentEnergy = $recent?->electricConsumption ?? $energy->recent50Km;
            $recentEnergyUnit = $recent?->electricConsumptionUnit ?? 'kWh';

            echo 'Total mileage: '.value($totalMileage, ' '.($mileageUnit ?? 'km'))."\n";
            optional('Electric mileage', $energy->electricMileage, ' km');
            optional('Fuel mileage', $energy->fuelMileage, ' km');
            optional('Total energy', $energy->totalEnergy, ' kWh');
            echo 'Cumulative EV average: '.value($cumulativeAverage, ' '.($cumulativeUnit ?? ''))."\n";
            echo 'Recent EV average: '.value($recentAverage, ' '.($recentAverageUnit ?? ''))."\n";
            echo 'Recent EV energy: '.value($recentEnergy, ' '.($recentEnergyUnit ?? 'kWh'))."\n";

            if ($energy->vehicleGraph !== null) {
                echo 'Vehicle history: '.implode(', ', $energy->vehicleGraph->values).' '.($energy->vehicleGraph->unit ?? '')."\n";
            }

            if ($energy->modelGraph !== null) {
                echo 'Model history: '.implode(', ', $energy->modelGraph->values).' '.($energy->modelGraph->unit ?? '')."\n";
            }
            optional('CO₂ saved', $energy->co2Saved);
        });

        section('Push notifications', function () use ($client, $vin): void {
            $notifications = $client->notifications($vin)->state();
            $enabled = $notifications->isEnabled();

            echo 'Vehicle status notifications: '.($enabled === null ? 'not configured' : ($enabled ? 'enabled' : 'disabled'))."\n";
            foreach ($notifications->switches as $switch) {
                echo sprintf("Type %d: %s\n", $switch->type, $switch->isEnabled() ? 'enabled' : 'disabled');
            }
        });
    }
} catch (BydException $exception) {
    fwrite(STDERR, 'BYD API error: '.$exception->getMessage()."\n");
    exit(1);
}

/*
 * Commands below change the vehicle state and are intentionally not executed.
 * Copy only the command you actually need:
 *
 * $client->controls($vin)->verifyPin();
 * $client->controls($vin)->lock();
 * $client->controls($vin)->unlock();
 * $client->controls($vin)->flashLights();
 * $client->controls($vin)->findCar();
 * $client->controls($vin)->openWindows();
 * $client->controls($vin)->closeWindows();
 * $client->controls($vin)->openTrunk();
 * $client->controls($vin)->closeTrunk();
 *
 * $client->climate($vin)->start(new \Byd\ApiClient\Dto\Request\ClimateStartRequest(
 *     temperature: 22.0,
 *     durationMinutes: 15,
 * ));
 * $client->climate($vin)->stop();
 *
 * $client->charging($vin)->setSmartCharging(true);
 * $client->charging($vin)->start();
 * $client->settings($vin)->rename('My BYD');
 */
