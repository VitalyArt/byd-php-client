<?php

declare(strict_types=1);

namespace Byd\ApiClient\Models;

use function count;

use DateTimeImmutable;
use DateTimeInterface;

use function in_array;

/**
 * Realtime telemetry data for a vehicle.
 */
class VehicleRealtimeData extends BaseModel
{
    // Connection & state
    private int $onlineState = -1;
    private int $connectState = -1;
    private int $vehicleState = -1;
    private ?string $requestSerial = null;

    // Battery & range
    private ?float $elecPercent = null;
    private ?float $powerBattery = null;
    private ?float $enduranceMileage = null;
    private ?float $evEndurance = null;
    private ?float $enduranceMileageV2 = null;
    private ?string $enduranceMileageV2Unit = null;
    private ?float $totalMileage = null;
    private ?float $totalMileageV2 = null;
    private ?string $totalMileageV2Unit = null;

    // Driving
    private ?float $speed = null;
    private ?int $powerGear = null;

    // Climate
    private ?float $tempInCar = null;
    private ?int $mainSettingTemp = null;
    private ?float $mainSettingTempNew = null;
    private ?int $airRunState = null;

    // Seat heating/ventilation
    private ?int $mainSeatHeatState = null;
    private ?int $mainSeatVentilationState = null;
    private ?int $copilotSeatHeatState = null;
    private ?int $copilotSeatVentilationState = null;
    private ?int $steeringWheelHeatState = null;
    private ?int $lrSeatHeatState = null;
    private ?int $lrSeatVentilationState = null;
    private ?int $rrSeatHeatState = null;
    private ?int $rrSeatVentilationState = null;

    // Charging
    private int $chargingState = -1;
    private ?int $chargeState = null;
    private ?int $waitStatus = null;
    private ?int $fullHour = null;
    private ?int $fullMinute = null;
    private ?int $remainingHours = null;
    private ?int $remainingMinutes = null;
    private ?int $bookingChargeState = null;
    private ?int $bookingChargingHour = null;
    private ?int $bookingChargingMinute = null;

    // Doors
    private ?int $leftFrontDoor = null;
    private ?int $rightFrontDoor = null;
    private ?int $leftRearDoor = null;
    private ?int $rightRearDoor = null;
    private ?int $trunkLid = null;
    private ?int $slidingDoor = null;
    private ?int $forehold = null;

    // Locks
    private ?int $leftFrontDoorLock = null;
    private ?int $rightFrontDoorLock = null;
    private ?int $leftRearDoorLock = null;
    private ?int $rightRearDoorLock = null;
    private ?int $slidingDoorLock = null;

    // Windows
    private ?int $leftFrontWindow = null;
    private ?int $rightFrontWindow = null;
    private ?int $leftRearWindow = null;
    private ?int $rightRearWindow = null;
    private ?int $skylight = null;

    // Tire pressure
    private ?float $leftFrontTirePressure = null;
    private ?float $rightFrontTirePressure = null;
    private ?float $leftRearTirePressure = null;
    private ?float $rightRearTirePressure = null;
    private ?int $leftFrontTireStatus = null;
    private ?int $rightFrontTireStatus = null;
    private ?int $leftRearTireStatus = null;
    private ?int $rightRearTireStatus = null;
    private ?int $tirePressUnit = null;
    private ?int $tirepressureSystem = null;
    private ?int $rapidTireLeak = null;

    // Energy consumption
    private ?float $totalPower = null;
    private ?float $gl = null;
    private ?string $totalEnergy = null;
    private ?string $nearestEnergyConsumption = null;
    private ?string $nearestEnergyConsumptionUnit = null;
    private ?string $recent50kmEnergy = null;

    // Fuel (hybrid vehicles)
    private ?float $oilEndurance = null;
    private ?float $oilPercent = null;
    private ?float $totalOil = null;

    // System indicators
    private ?int $powerSystem = null;
    private ?int $engineStatus = null;
    private ?int $epb = null;
    private ?int $eps = null;
    private ?int $esp = null;
    private ?int $absWarning = null;
    private ?int $svs = null;
    private ?int $srs = null;
    private ?int $ect = null;
    private ?int $ectValue = null;
    private ?int $pwr = null;

    // Feature states
    private ?int $sentryStatus = null;
    private ?int $batteryHeatState = null;
    private ?int $chargeHeatState = null;
    private ?int $upgradeStatus = null;

    // Metadata
    private ?DateTimeInterface $timestamp = null;

    /**
     * @param array<string, mixed> $data
     */
    protected function populate(array $data): void
    {
        // Apply key aliases
        $aliases = [
            'backCover' => 'trunkLid',
            'leftFrontTirepressure' => 'leftFrontTirePressure',
            'rightFrontTirepressure' => 'rightFrontTirePressure',
            'leftRearTirepressure' => 'leftRearTirePressure',
            'rightRearTirepressure' => 'rightRearTirePressure',
            'abs' => 'absWarning',
            'time' => 'timestamp',
            'recent50kmEnergy' => 'recent50KmEnergy',
            'stearingWheelHeatState' => 'steeringWheelHeatState',
        ];

        foreach ($aliases as $oldKey => $newKey) {
            if (isset($data[$oldKey]) && !isset($data[$newKey])) {
                $data[$newKey] = $data[$oldKey];
                unset($data[$oldKey]);
            }
        }

        // Connection & state
        $this->onlineState = (int) ($data['onlineState'] ?? -1);
        $this->connectState = (int) ($data['connectState'] ?? -1);
        $this->vehicleState = (int) ($data['vehicleState'] ?? -1);
        $this->requestSerial = isset($data['requestSerial']) ? (string) $data['requestSerial'] : null;

        // Battery & range
        $this->elecPercent = isset($data['elecPercent']) ? (float) $data['elecPercent'] : null;
        $this->powerBattery = isset($data['powerBattery']) ? (float) $data['powerBattery'] : null;
        $this->enduranceMileage = isset($data['enduranceMileage']) ? (float) $data['enduranceMileage'] : null;
        $this->evEndurance = isset($data['evEndurance']) ? (float) $data['evEndurance'] : null;
        $this->enduranceMileageV2 = isset($data['enduranceMileageV2']) ? (float) $data['enduranceMileageV2'] : null;
        $this->enduranceMileageV2Unit = isset($data['enduranceMileageV2Unit']) ? (string) $data['enduranceMileageV2Unit'] : null;
        $this->totalMileage = isset($data['totalMileage']) ? (float) $data['totalMileage'] : null;
        $this->totalMileageV2 = isset($data['totalMileageV2']) ? (float) $data['totalMileageV2'] : null;
        $this->totalMileageV2Unit = isset($data['totalMileageV2Unit']) ? (string) $data['totalMileageV2Unit'] : null;

        // Driving
        $this->speed = isset($data['speed']) ? (float) $data['speed'] : null;
        $this->powerGear = isset($data['powerGear']) ? (int) $data['powerGear'] : null;

        // Climate
        $this->tempInCar = isset($data['tempInCar']) ? (float) $data['tempInCar'] : null;
        $this->mainSettingTemp = isset($data['mainSettingTemp']) ? (int) $data['mainSettingTemp'] : null;
        $this->mainSettingTempNew = isset($data['mainSettingTempNew']) ? (float) $data['mainSettingTempNew'] : null;
        $this->airRunState = isset($data['airRunState']) ? (int) $data['airRunState'] : null;

        // Seat heating/ventilation
        $this->mainSeatHeatState = isset($data['mainSeatHeatState']) ? (int) $data['mainSeatHeatState'] : null;
        $this->mainSeatVentilationState = isset($data['mainSeatVentilationState']) ? (int) $data['mainSeatVentilationState'] : null;
        $this->copilotSeatHeatState = isset($data['copilotSeatHeatState']) ? (int) $data['copilotSeatHeatState'] : null;
        $this->copilotSeatVentilationState = isset($data['copilotSeatVentilationState']) ? (int) $data['copilotSeatVentilationState'] : null;
        $this->steeringWheelHeatState = isset($data['steeringWheelHeatState']) ? (int) $data['steeringWheelHeatState'] : null;
        $this->lrSeatHeatState = isset($data['lrSeatHeatState']) ? (int) $data['lrSeatHeatState'] : null;
        $this->lrSeatVentilationState = isset($data['lrSeatVentilationState']) ? (int) $data['lrSeatVentilationState'] : null;
        $this->rrSeatHeatState = isset($data['rrSeatHeatState']) ? (int) $data['rrSeatHeatState'] : null;
        $this->rrSeatVentilationState = isset($data['rrSeatVentilationState']) ? (int) $data['rrSeatVentilationState'] : null;

        // Charging
        $this->chargingState = (int) ($data['chargingState'] ?? -1);
        $this->chargeState = isset($data['chargeState']) ? (int) $data['chargeState'] : null;
        $this->waitStatus = isset($data['waitStatus']) ? (int) $data['waitStatus'] : null;
        $this->fullHour = isset($data['fullHour']) ? (int) $data['fullHour'] : null;
        $this->fullMinute = isset($data['fullMinute']) ? (int) $data['fullMinute'] : null;
        $this->remainingHours = isset($data['remainingHours']) ? (int) $data['remainingHours'] : null;
        $this->remainingMinutes = isset($data['remainingMinutes']) ? (int) $data['remainingMinutes'] : null;
        $this->bookingChargeState = isset($data['bookingChargeState']) ? (int) $data['bookingChargeState'] : null;
        $this->bookingChargingHour = isset($data['bookingChargingHour']) ? (int) $data['bookingChargingHour'] : null;
        $this->bookingChargingMinute = isset($data['bookingChargingMinute']) ? (int) $data['bookingChargingMinute'] : null;

        // Doors
        $this->leftFrontDoor = isset($data['leftFrontDoor']) ? (int) $data['leftFrontDoor'] : null;
        $this->rightFrontDoor = isset($data['rightFrontDoor']) ? (int) $data['rightFrontDoor'] : null;
        $this->leftRearDoor = isset($data['leftRearDoor']) ? (int) $data['leftRearDoor'] : null;
        $this->rightRearDoor = isset($data['rightRearDoor']) ? (int) $data['rightRearDoor'] : null;
        $this->trunkLid = isset($data['trunkLid']) ? (int) $data['trunkLid'] : null;
        $this->slidingDoor = isset($data['slidingDoor']) ? (int) $data['slidingDoor'] : null;
        $this->forehold = isset($data['forehold']) ? (int) $data['forehold'] : null;

        // Locks
        $this->leftFrontDoorLock = isset($data['leftFrontDoorLock']) ? (int) $data['leftFrontDoorLock'] : null;
        $this->rightFrontDoorLock = isset($data['rightFrontDoorLock']) ? (int) $data['rightFrontDoorLock'] : null;
        $this->leftRearDoorLock = isset($data['leftRearDoorLock']) ? (int) $data['leftRearDoorLock'] : null;
        $this->rightRearDoorLock = isset($data['rightRearDoorLock']) ? (int) $data['rightRearDoorLock'] : null;
        $this->slidingDoorLock = isset($data['slidingDoorLock']) ? (int) $data['slidingDoorLock'] : null;

        // Windows
        $this->leftFrontWindow = isset($data['leftFrontWindow']) ? (int) $data['leftFrontWindow'] : null;
        $this->rightFrontWindow = isset($data['rightFrontWindow']) ? (int) $data['rightFrontWindow'] : null;
        $this->leftRearWindow = isset($data['leftRearWindow']) ? (int) $data['leftRearWindow'] : null;
        $this->rightRearWindow = isset($data['rightRearWindow']) ? (int) $data['rightRearWindow'] : null;
        $this->skylight = isset($data['skylight']) ? (int) $data['skylight'] : null;

        // Tire pressure
        $this->leftFrontTirePressure = isset($data['leftFrontTirePressure']) ? (float) $data['leftFrontTirePressure'] : null;
        $this->rightFrontTirePressure = isset($data['rightFrontTirePressure']) ? (float) $data['rightFrontTirePressure'] : null;
        $this->leftRearTirePressure = isset($data['leftRearTirePressure']) ? (float) $data['leftRearTirePressure'] : null;
        $this->rightRearTirePressure = isset($data['rightRearTirePressure']) ? (float) $data['rightRearTirePressure'] : null;
        $this->leftFrontTireStatus = isset($data['leftFrontTireStatus']) ? (int) $data['leftFrontTireStatus'] : null;
        $this->rightFrontTireStatus = isset($data['rightFrontTireStatus']) ? (int) $data['rightFrontTireStatus'] : null;
        $this->leftRearTireStatus = isset($data['leftRearTireStatus']) ? (int) $data['leftRearTireStatus'] : null;
        $this->rightRearTireStatus = isset($data['rightRearTireStatus']) ? (int) $data['rightRearTireStatus'] : null;
        $this->tirePressUnit = isset($data['tirePressUnit']) ? (int) $data['tirePressUnit'] : null;
        $this->tirepressureSystem = isset($data['tirepressureSystem']) ? (int) $data['tirepressureSystem'] : null;
        $this->rapidTireLeak = isset($data['rapidTireLeak']) ? (int) $data['rapidTireLeak'] : null;

        // Energy consumption
        $this->totalPower = isset($data['totalPower']) ? (float) $data['totalPower'] : null;
        $this->gl = isset($data['gl']) ? (float) $data['gl'] : null;
        $this->totalEnergy = isset($data['totalEnergy']) ? (string) $data['totalEnergy'] : null;
        $this->nearestEnergyConsumption = isset($data['nearestEnergyConsumption']) ? (string) $data['nearestEnergyConsumption'] : null;
        $this->nearestEnergyConsumptionUnit = isset($data['nearestEnergyConsumptionUnit']) ? (string) $data['nearestEnergyConsumptionUnit'] : null;
        $this->recent50kmEnergy = isset($data['recent50kmEnergy']) ? (string) $data['recent50kmEnergy'] : null;

        // Fuel (hybrid vehicles)
        $this->oilEndurance = isset($data['oilEndurance']) ? (float) $data['oilEndurance'] : null;
        $this->oilPercent = isset($data['oilPercent']) ? (float) $data['oilPercent'] : null;
        $this->totalOil = isset($data['totalOil']) ? (float) $data['totalOil'] : null;

        // System indicators
        $this->powerSystem = isset($data['powerSystem']) ? (int) $data['powerSystem'] : null;
        $this->engineStatus = isset($data['engineStatus']) ? (int) $data['engineStatus'] : null;
        $this->epb = isset($data['epb']) ? (int) $data['epb'] : null;
        $this->eps = isset($data['eps']) ? (int) $data['eps'] : null;
        $this->esp = isset($data['esp']) ? (int) $data['esp'] : null;
        $this->absWarning = isset($data['absWarning']) ? (int) $data['absWarning'] : null;
        $this->svs = isset($data['svs']) ? (int) $data['svs'] : null;
        $this->srs = isset($data['srs']) ? (int) $data['srs'] : null;
        $this->ect = isset($data['ect']) ? (int) $data['ect'] : null;
        $this->ectValue = isset($data['ectValue']) ? (int) $data['ectValue'] : null;
        $this->pwr = isset($data['pwr']) ? (int) $data['pwr'] : null;

        // Feature states
        $this->sentryStatus = isset($data['sentryStatus']) ? (int) $data['sentryStatus'] : null;
        $this->batteryHeatState = isset($data['batteryHeatState']) ? (int) $data['batteryHeatState'] : null;
        $this->chargeHeatState = isset($data['chargeHeatState']) ? (int) $data['chargeHeatState'] : null;
        $this->upgradeStatus = isset($data['upgradeStatus']) ? (int) $data['upgradeStatus'] : null;

        // Metadata
        if (isset($data['timestamp'])) {
            $this->timestamp = $this->parseTimestamp($data['timestamp']);
        }
    }

    private function parseTimestamp($timestamp): ?DateTimeInterface
    {
        if ($timestamp === null) {
            return null;
        }

        $ts = (int) $timestamp;
        // Threshold to distinguish seconds from milliseconds.
        if ($ts >= 1000000000000) {
            $ts = (int) ($ts / 1000);
        }

        return DateTimeImmutable::createFromFormat('U', (string) $ts);
    }

    /**
     * Check if a raw realtime payload appears to contain meaningful data.
     *
     * @param array<string, mixed> $vehicleInfo
     */
    public static function isReadyRaw(array $vehicleInfo): bool
    {
        if (empty($vehicleInfo)) {
            return false;
        }

        if (isset($vehicleInfo['onlineState']) && (int) $vehicleInfo['onlineState'] === 2) { // OFFLINE
            return false;
        }

        $tireFields = [
            'leftFrontTirepressure',
            'rightFrontTirepressure',
            'leftRearTirepressure',
            'rightRearTirepressure',
        ];

        foreach ($tireFields as $field) {
            if (isset($vehicleInfo[$field]) && (float) ($vehicleInfo[$field] ?? 0) > 0) {
                return true;
            }
        }

        if (isset($vehicleInfo['time']) && (int) ($vehicleInfo['time'] ?? 0) > 0) {
            return true;
        }

        return isset($vehicleInfo['enduranceMileage']) && (float) ($vehicleInfo['enduranceMileage'] ?? 0) > 0;
    }

    // Convenience properties
    public function isOnline(): bool
    {
        return $this->onlineState === 1; // ONLINE
    }

    public function isCharging(): bool
    {
        return $this->chargingState === 1; // CHARGING
    }

    public function getTimeToFullMinutes(): ?int
    {
        if ($this->fullHour === null || $this->fullMinute === null) {
            return null;
        }

        return $this->fullHour * 60 + $this->fullMinute;
    }

    public function isInteriorTempAvailable(): bool
    {
        // After sentinel normalisation tempInCar is null when
        // the BYD API returned -129, so a simple is not null suffices.
        return $this->tempInCar !== null;
    }

    public function isLocked(): ?bool
    {
        $locks = [
            $this->leftFrontDoorLock,
            $this->rightFrontDoorLock,
            $this->leftRearDoorLock,
            $this->rightRearDoorLock,
        ];

        $skipValues = [null, -1, 0]; // null, UNKNOWN, UNAVAILABLE
        $known = array_filter($locks, function ($lk) use ($skipValues) {
            return !in_array($lk, $skipValues, true);
        });

        if (empty($known)) {
            return null;
        }

        // LOCKED = 2
        return count(array_filter($known, function ($lk) {
            return $lk === 2;
        })) === count($known);
    }

    public function isAnyDoorOpen(): bool
    {
        $doors = [
            $this->leftFrontDoor,
            $this->rightFrontDoor,
            $this->leftRearDoor,
            $this->rightRearDoor,
            $this->trunkLid,
            $this->slidingDoor,
            $this->forehold,
        ];

        // OPEN = 1
        return count(array_filter($doors, function ($d) {
            return $d === 1;
        })) > 0;
    }

    public function isAnyWindowOpen(): bool
    {
        $windows = [
            $this->leftFrontWindow,
            $this->rightFrontWindow,
            $this->leftRearWindow,
            $this->rightRearWindow,
            $this->skylight,
        ];

        // OPEN = 2
        return count(array_filter($windows, function ($w) {
            return $w === 2;
        })) > 0;
    }

    public function isVehicleOn(): bool
    {
        return $this->powerGear === 3; // ON
    }

    public function isBatteryHeating(): ?bool
    {
        if ($this->batteryHeatState === null) {
            return null;
        }

        return (bool) $this->batteryHeatState;
    }

    public function isSteeringWheelHeating(): ?bool
    {
        if ($this->steeringWheelHeatState === null) {
            return null;
        }

        // ON = -1 (makes no sense, but confirmed)
        return $this->steeringWheelHeatState === -1;
    }

    // Getters
    public function getOnlineState(): int
    {
        return $this->onlineState;
    }

    public function getConnectState(): int
    {
        return $this->connectState;
    }

    public function getVehicleState(): int
    {
        return $this->vehicleState;
    }

    public function getRequestSerial(): ?string
    {
        return $this->requestSerial;
    }

    public function getElecPercent(): ?float
    {
        return $this->elecPercent;
    }

    public function getPowerBattery(): ?float
    {
        return $this->powerBattery;
    }

    public function getEnduranceMileage(): ?float
    {
        return $this->enduranceMileage;
    }

    public function getEvEndurance(): ?float
    {
        return $this->evEndurance;
    }

    public function getEnduranceMileageV2(): ?float
    {
        return $this->enduranceMileageV2;
    }

    public function getEnduranceMileageV2Unit(): ?string
    {
        return $this->enduranceMileageV2Unit;
    }

    public function getTotalMileage(): ?float
    {
        return $this->totalMileage;
    }

    public function getTotalMileageV2(): ?float
    {
        return $this->totalMileageV2;
    }

    public function getTotalMileageV2Unit(): ?string
    {
        return $this->totalMileageV2Unit;
    }

    public function getSpeed(): ?float
    {
        return $this->speed;
    }

    public function getPowerGear(): ?int
    {
        return $this->powerGear;
    }

    public function getTempInCar(): ?float
    {
        return $this->tempInCar;
    }

    public function getMainSettingTemp(): ?int
    {
        return $this->mainSettingTemp;
    }

    public function getMainSettingTempNew(): ?float
    {
        return $this->mainSettingTempNew;
    }

    public function getAirRunState(): ?int
    {
        return $this->airRunState;
    }

    public function getMainSeatHeatState(): ?int
    {
        return $this->mainSeatHeatState;
    }

    public function getMainSeatVentilationState(): ?int
    {
        return $this->mainSeatVentilationState;
    }

    public function getCopilotSeatHeatState(): ?int
    {
        return $this->copilotSeatHeatState;
    }

    public function getCopilotSeatVentilationState(): ?int
    {
        return $this->copilotSeatVentilationState;
    }

    public function getSteeringWheelHeatState(): ?int
    {
        return $this->steeringWheelHeatState;
    }

    public function getLrSeatHeatState(): ?int
    {
        return $this->lrSeatHeatState;
    }

    public function getLrSeatVentilationState(): ?int
    {
        return $this->lrSeatVentilationState;
    }

    public function getRrSeatHeatState(): ?int
    {
        return $this->rrSeatHeatState;
    }

    public function getRrSeatVentilationState(): ?int
    {
        return $this->rrSeatVentilationState;
    }

    public function getChargingState(): int
    {
        return $this->chargingState;
    }

    public function getChargeState(): ?int
    {
        return $this->chargeState;
    }

    public function getWaitStatus(): ?int
    {
        return $this->waitStatus;
    }

    public function getFullHour(): ?int
    {
        return $this->fullHour;
    }

    public function getFullMinute(): ?int
    {
        return $this->fullMinute;
    }

    public function getRemainingHours(): ?int
    {
        return $this->remainingHours;
    }

    public function getRemainingMinutes(): ?int
    {
        return $this->remainingMinutes;
    }

    public function getBookingChargeState(): ?int
    {
        return $this->bookingChargeState;
    }

    public function getBookingChargingHour(): ?int
    {
        return $this->bookingChargingHour;
    }

    public function getBookingChargingMinute(): ?int
    {
        return $this->bookingChargingMinute;
    }

    public function getLeftFrontDoor(): ?int
    {
        return $this->leftFrontDoor;
    }

    public function getRightFrontDoor(): ?int
    {
        return $this->rightFrontDoor;
    }

    public function getLeftRearDoor(): ?int
    {
        return $this->leftRearDoor;
    }

    public function getRightRearDoor(): ?int
    {
        return $this->rightRearDoor;
    }

    public function getTrunkLid(): ?int
    {
        return $this->trunkLid;
    }

    public function getSlidingDoor(): ?int
    {
        return $this->slidingDoor;
    }

    public function getForehold(): ?int
    {
        return $this->forehold;
    }

    public function getLeftFrontDoorLock(): ?int
    {
        return $this->leftFrontDoorLock;
    }

    public function getRightFrontDoorLock(): ?int
    {
        return $this->rightFrontDoorLock;
    }

    public function getLeftRearDoorLock(): ?int
    {
        return $this->leftRearDoorLock;
    }

    public function getRightRearDoorLock(): ?int
    {
        return $this->rightRearDoorLock;
    }

    public function getSlidingDoorLock(): ?int
    {
        return $this->slidingDoorLock;
    }

    public function getLeftFrontWindow(): ?int
    {
        return $this->leftFrontWindow;
    }

    public function getRightFrontWindow(): ?int
    {
        return $this->rightFrontWindow;
    }

    public function getLeftRearWindow(): ?int
    {
        return $this->leftRearWindow;
    }

    public function getRightRearWindow(): ?int
    {
        return $this->rightRearWindow;
    }

    public function getSkylight(): ?int
    {
        return $this->skylight;
    }

    public function getLeftFrontTirePressure(): ?float
    {
        return $this->leftFrontTirePressure;
    }

    public function getRightFrontTirePressure(): ?float
    {
        return $this->rightFrontTirePressure;
    }

    public function getLeftRearTirePressure(): ?float
    {
        return $this->leftRearTirePressure;
    }

    public function getRightRearTirePressure(): ?float
    {
        return $this->rightRearTirePressure;
    }

    public function getLeftFrontTireStatus(): ?int
    {
        return $this->leftFrontTireStatus;
    }

    public function getRightFrontTireStatus(): ?int
    {
        return $this->rightFrontTireStatus;
    }

    public function getLeftRearTireStatus(): ?int
    {
        return $this->leftRearTireStatus;
    }

    public function getRightRearTireStatus(): ?int
    {
        return $this->rightRearTireStatus;
    }

    public function getTirePressUnit(): ?int
    {
        return $this->tirePressUnit;
    }

    public function getTirepressureSystem(): ?int
    {
        return $this->tirepressureSystem;
    }

    public function getRapidTireLeak(): ?int
    {
        return $this->rapidTireLeak;
    }

    public function getTotalPower(): ?float
    {
        return $this->totalPower;
    }

    public function getGl(): ?float
    {
        return $this->gl;
    }

    public function getTotalEnergy(): ?string
    {
        return $this->totalEnergy;
    }

    public function getNearestEnergyConsumption(): ?string
    {
        return $this->nearestEnergyConsumption;
    }

    public function getNearestEnergyConsumptionUnit(): ?string
    {
        return $this->nearestEnergyConsumptionUnit;
    }

    public function getRecent50kmEnergy(): ?string
    {
        return $this->recent50kmEnergy;
    }

    public function getOilEndurance(): ?float
    {
        return $this->oilEndurance;
    }

    public function getOilPercent(): ?float
    {
        return $this->oilPercent;
    }

    public function getTotalOil(): ?float
    {
        return $this->totalOil;
    }

    public function getPowerSystem(): ?int
    {
        return $this->powerSystem;
    }

    public function getEngineStatus(): ?int
    {
        return $this->engineStatus;
    }

    public function getEpb(): ?int
    {
        return $this->epb;
    }

    public function getEps(): ?int
    {
        return $this->eps;
    }

    public function getEsp(): ?int
    {
        return $this->esp;
    }

    public function getAbsWarning(): ?int
    {
        return $this->absWarning;
    }

    public function getSvs(): ?int
    {
        return $this->svs;
    }

    public function getSrs(): ?int
    {
        return $this->srs;
    }

    public function getEct(): ?int
    {
        return $this->ect;
    }

    public function getEctValue(): ?int
    {
        return $this->ectValue;
    }

    public function getPwr(): ?int
    {
        return $this->pwr;
    }

    public function getSentryStatus(): ?int
    {
        return $this->sentryStatus;
    }

    public function getBatteryHeatState(): ?int
    {
        return $this->batteryHeatState;
    }

    public function getChargeHeatState(): ?int
    {
        return $this->chargeHeatState;
    }

    public function getUpgradeStatus(): ?int
    {
        return $this->upgradeStatus;
    }

    public function getTimestamp(): ?DateTimeInterface
    {
        return $this->timestamp;
    }
}
