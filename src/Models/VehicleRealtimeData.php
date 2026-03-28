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
    private OnlineState $onlineState = OnlineState::UNKNOWN;

    private ConnectState $connectState = ConnectState::UNKNOWN;

    private VehicleState $vehicleState = VehicleState::UNKNOWN;

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

    private PowerGear $powerGear = PowerGear::UNKNOWN;

    // Climate
    private ?float $tempInCar = null;

    private ?int $mainSettingTemp = null;

    private ?float $mainSettingTempNew = null;

    private AirCirculationMode $airRunState = AirCirculationMode::UNKNOWN;

    // Seat heating/ventilation
    private SeatHeatVentState $mainSeatHeatState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $mainSeatVentilationState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $copilotSeatHeatState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $copilotSeatVentilationState = SeatHeatVentState::UNKNOWN;

    private StearingWheelHeat $steeringWheelHeatState = StearingWheelHeat::OFF;

    private SeatHeatVentState $lrSeatHeatState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $lrSeatVentilationState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $lrThirdHeatState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $lrThirdVentilationState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $rrSeatHeatState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $rrSeatVentilationState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $rrThirdHeatState = SeatHeatVentState::UNKNOWN;

    private SeatHeatVentState $rrThirdVentilationState = SeatHeatVentState::UNKNOWN;

    // Charging
    private ChargingState $chargingState = ChargingState::UNKNOWN;

    private ChargingState $chargeState = ChargingState::UNKNOWN;

    private ?int $waitStatus = null;

    private ?int $fullHour = null;

    private ?int $fullMinute = null;

    private ?int $remainingHours = null;

    private ?int $remainingMinutes = null;

    private ?int $bookingChargeState = null;

    private ?int $bookingChargingHour = null;

    private ?int $bookingChargingMinute = null;

    // Doors
    private DoorOpenState $leftFrontDoor = DoorOpenState::UNKNOWN;

    private DoorOpenState $rightFrontDoor = DoorOpenState::UNKNOWN;

    private DoorOpenState $leftRearDoor = DoorOpenState::UNKNOWN;

    private DoorOpenState $rightRearDoor = DoorOpenState::UNKNOWN;

    private DoorOpenState $trunkLid = DoorOpenState::UNKNOWN;

    private DoorOpenState $slidingDoor = DoorOpenState::UNKNOWN;

    private DoorOpenState $forehold = DoorOpenState::UNKNOWN;

    // Locks
    private LockState $leftFrontDoorLock = LockState::UNKNOWN;

    private LockState $rightFrontDoorLock = LockState::UNKNOWN;

    private LockState $leftRearDoorLock = LockState::UNKNOWN;

    private LockState $rightRearDoorLock = LockState::UNKNOWN;

    private LockState $slidingDoorLock = LockState::UNKNOWN;

    // Windows
    private WindowState $leftFrontWindow = WindowState::UNKNOWN;

    private WindowState $rightFrontWindow = WindowState::UNKNOWN;

    private WindowState $leftRearWindow = WindowState::UNKNOWN;

    private WindowState $rightRearWindow = WindowState::UNKNOWN;

    private WindowState $skylight = WindowState::UNKNOWN;

    // Tire pressure
    private ?float $leftFrontTirePressure = null;

    private ?float $rightFrontTirePressure = null;

    private ?float $leftRearTirePressure = null;

    private ?float $rightRearTirePressure = null;

    private ?int $leftFrontTireStatus = null;

    private ?int $rightFrontTireStatus = null;

    private ?int $leftRearTireStatus = null;

    private ?int $rightRearTireStatus = null;

    private TirePressureUnit $tirePressUnit = TirePressureUnit::UNKNOWN;

    private ?int $tirepressureSystem = null;

    private ?int $rapidTireLeak = null;

    // Energy consumption
    private ?float $totalPower = null;

    private ?float $gl = null;

    private ?string $totalEnergy = null;

    private ?string $nearestEnergyConsumption = null;

    private ?string $nearestEnergyConsumptionUnit = null;

    private ?string $recent50kmEnergy = null;

    private ?float $energyConsumption = null;

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
        // Apply key aliases (BYD API uses inconsistent naming)
        $aliases = [
            'backCover' => 'trunkLid',
            'leftFrontTirepressure' => 'leftFrontTirePressure',
            'rightFrontTirepressure' => 'rightFrontTirePressure',
            'leftRearTirepressure' => 'leftRearTirePressure',
            'rightRearTirepressure' => 'rightRearTirePressure',
            'abs' => 'absWarning',
            'time' => 'timestamp',
            'stearingWheelHeatState' => 'steeringWheelHeatState',
        ];

        foreach ($aliases as $oldKey => $newKey) {
            if (isset($data[$oldKey]) && !isset($data[$newKey])) {
                $data[$newKey] = $data[$oldKey];
                unset($data[$oldKey]);
            }
        }

        // Connection & state
        $this->onlineState = OnlineState::tryFrom((int) ($data['onlineState'] ?? -1)) ?? OnlineState::UNKNOWN;
        $this->connectState = ConnectState::tryFrom((int) ($data['connectState'] ?? -1)) ?? ConnectState::UNKNOWN;
        $this->vehicleState = VehicleState::tryFrom((int) ($data['vehicleState'] ?? -1)) ?? VehicleState::UNKNOWN;
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
        $this->powerGear = PowerGear::tryFrom((int) ($data['powerGear'] ?? -1)) ?? PowerGear::UNKNOWN;

        // Climate — tempInCar uses -129 as "no data" sentinel
        $rawTempInCar = isset($data['tempInCar']) ? (float) $data['tempInCar'] : null;
        $this->tempInCar = ($rawTempInCar !== null && $rawTempInCar <= -100.0) ? null : $rawTempInCar;

        $this->mainSettingTemp = isset($data['mainSettingTemp']) ? (int) $data['mainSettingTemp'] : null;
        $this->mainSettingTempNew = isset($data['mainSettingTempNew']) ? (float) $data['mainSettingTempNew'] : null;
        $this->airRunState = AirCirculationMode::tryFrom((int) ($data['airRunState'] ?? -1)) ?? AirCirculationMode::UNKNOWN;

        // Seat heating/ventilation
        $this->mainSeatHeatState = SeatHeatVentState::tryFrom((int) ($data['mainSeatHeatState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->mainSeatVentilationState = SeatHeatVentState::tryFrom((int) ($data['mainSeatVentilationState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->copilotSeatHeatState = SeatHeatVentState::tryFrom((int) ($data['copilotSeatHeatState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->copilotSeatVentilationState = SeatHeatVentState::tryFrom((int) ($data['copilotSeatVentilationState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->steeringWheelHeatState = StearingWheelHeat::tryFrom((int) ($data['steeringWheelHeatState'] ?? 1)) ?? StearingWheelHeat::OFF;
        $this->lrSeatHeatState = SeatHeatVentState::tryFrom((int) ($data['lrSeatHeatState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->lrSeatVentilationState = SeatHeatVentState::tryFrom((int) ($data['lrSeatVentilationState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->lrThirdHeatState = SeatHeatVentState::tryFrom((int) ($data['lrThirdHeatState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->lrThirdVentilationState = SeatHeatVentState::tryFrom((int) ($data['lrThirdVentilationState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->rrSeatHeatState = SeatHeatVentState::tryFrom((int) ($data['rrSeatHeatState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->rrSeatVentilationState = SeatHeatVentState::tryFrom((int) ($data['rrSeatVentilationState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->rrThirdHeatState = SeatHeatVentState::tryFrom((int) ($data['rrThirdHeatState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;
        $this->rrThirdVentilationState = SeatHeatVentState::tryFrom((int) ($data['rrThirdVentilationState'] ?? -1)) ?? SeatHeatVentState::UNKNOWN;

        // Charging — use -1 (UNKNOWN) sentinel as null for hours/minutes
        $this->chargingState = ChargingState::tryFrom((int) ($data['chargingState'] ?? -1)) ?? ChargingState::UNKNOWN;
        $this->chargeState = ChargingState::tryFrom((int) ($data['chargeState'] ?? -1)) ?? ChargingState::UNKNOWN;
        $this->waitStatus = isset($data['waitStatus']) ? (int) $data['waitStatus'] : null;

        $rawFullHour = isset($data['fullHour']) ? (int) $data['fullHour'] : null;
        $this->fullHour = ($rawFullHour !== null && $rawFullHour < 0) ? null : $rawFullHour;

        $rawFullMinute = isset($data['fullMinute']) ? (int) $data['fullMinute'] : null;
        $this->fullMinute = ($rawFullMinute !== null && $rawFullMinute < 0) ? null : $rawFullMinute;

        $rawRemainingHours = isset($data['remainingHours']) ? (int) $data['remainingHours'] : null;
        $this->remainingHours = ($rawRemainingHours !== null && $rawRemainingHours < 0) ? null : $rawRemainingHours;

        $rawRemainingMinutes = isset($data['remainingMinutes']) ? (int) $data['remainingMinutes'] : null;
        $this->remainingMinutes = ($rawRemainingMinutes !== null && $rawRemainingMinutes < 0) ? null : $rawRemainingMinutes;

        $this->bookingChargeState = isset($data['bookingChargeState']) ? (int) $data['bookingChargeState'] : null;
        $this->bookingChargingHour = isset($data['bookingChargingHour']) ? (int) $data['bookingChargingHour'] : null;
        $this->bookingChargingMinute = isset($data['bookingChargingMinute']) ? (int) $data['bookingChargingMinute'] : null;

        // Doors
        $this->leftFrontDoor = DoorOpenState::tryFrom((int) ($data['leftFrontDoor'] ?? -1)) ?? DoorOpenState::UNKNOWN;
        $this->rightFrontDoor = DoorOpenState::tryFrom((int) ($data['rightFrontDoor'] ?? -1)) ?? DoorOpenState::UNKNOWN;
        $this->leftRearDoor = DoorOpenState::tryFrom((int) ($data['leftRearDoor'] ?? -1)) ?? DoorOpenState::UNKNOWN;
        $this->rightRearDoor = DoorOpenState::tryFrom((int) ($data['rightRearDoor'] ?? -1)) ?? DoorOpenState::UNKNOWN;
        $this->trunkLid = DoorOpenState::tryFrom((int) ($data['trunkLid'] ?? -1)) ?? DoorOpenState::UNKNOWN;
        $this->slidingDoor = DoorOpenState::tryFrom((int) ($data['slidingDoor'] ?? -1)) ?? DoorOpenState::UNKNOWN;
        $this->forehold = DoorOpenState::tryFrom((int) ($data['forehold'] ?? -1)) ?? DoorOpenState::UNKNOWN;

        // Locks
        $this->leftFrontDoorLock = LockState::tryFrom((int) ($data['leftFrontDoorLock'] ?? -1)) ?? LockState::UNKNOWN;
        $this->rightFrontDoorLock = LockState::tryFrom((int) ($data['rightFrontDoorLock'] ?? -1)) ?? LockState::UNKNOWN;
        $this->leftRearDoorLock = LockState::tryFrom((int) ($data['leftRearDoorLock'] ?? -1)) ?? LockState::UNKNOWN;
        $this->rightRearDoorLock = LockState::tryFrom((int) ($data['rightRearDoorLock'] ?? -1)) ?? LockState::UNKNOWN;
        $this->slidingDoorLock = LockState::tryFrom((int) ($data['slidingDoorLock'] ?? -1)) ?? LockState::UNKNOWN;

        // Windows
        $this->leftFrontWindow = WindowState::tryFrom((int) ($data['leftFrontWindow'] ?? -1)) ?? WindowState::UNKNOWN;
        $this->rightFrontWindow = WindowState::tryFrom((int) ($data['rightFrontWindow'] ?? -1)) ?? WindowState::UNKNOWN;
        $this->leftRearWindow = WindowState::tryFrom((int) ($data['leftRearWindow'] ?? -1)) ?? WindowState::UNKNOWN;
        $this->rightRearWindow = WindowState::tryFrom((int) ($data['rightRearWindow'] ?? -1)) ?? WindowState::UNKNOWN;
        $this->skylight = WindowState::tryFrom((int) ($data['skylight'] ?? -1)) ?? WindowState::UNKNOWN;

        // Tire pressure
        $this->leftFrontTirePressure = isset($data['leftFrontTirePressure']) ? (float) $data['leftFrontTirePressure'] : null;
        $this->rightFrontTirePressure = isset($data['rightFrontTirePressure']) ? (float) $data['rightFrontTirePressure'] : null;
        $this->leftRearTirePressure = isset($data['leftRearTirePressure']) ? (float) $data['leftRearTirePressure'] : null;
        $this->rightRearTirePressure = isset($data['rightRearTirePressure']) ? (float) $data['rightRearTirePressure'] : null;
        $this->leftFrontTireStatus = isset($data['leftFrontTireStatus']) ? (int) $data['leftFrontTireStatus'] : null;
        $this->rightFrontTireStatus = isset($data['rightFrontTireStatus']) ? (int) $data['rightFrontTireStatus'] : null;
        $this->leftRearTireStatus = isset($data['leftRearTireStatus']) ? (int) $data['leftRearTireStatus'] : null;
        $this->rightRearTireStatus = isset($data['rightRearTireStatus']) ? (int) $data['rightRearTireStatus'] : null;
        $this->tirePressUnit = TirePressureUnit::tryFrom((int) ($data['tirePressUnit'] ?? -1)) ?? TirePressureUnit::UNKNOWN;
        $this->tirepressureSystem = isset($data['tirepressureSystem']) ? (int) $data['tirepressureSystem'] : null;
        $this->rapidTireLeak = isset($data['rapidTireLeak']) ? (int) $data['rapidTireLeak'] : null;

        // Energy consumption
        $this->totalPower = isset($data['totalPower']) ? (float) $data['totalPower'] : null;
        $this->gl = isset($data['gl']) ? (float) $data['gl'] : null;
        $this->totalEnergy = isset($data['totalEnergy']) ? (string) $data['totalEnergy'] : null;
        $this->nearestEnergyConsumption = isset($data['nearestEnergyConsumption']) ? (string) $data['nearestEnergyConsumption'] : null;
        $this->nearestEnergyConsumptionUnit = isset($data['nearestEnergyConsumptionUnit']) ? (string) $data['nearestEnergyConsumptionUnit'] : null;
        $this->recent50kmEnergy = isset($data['recent50kmEnergy']) ? (string) $data['recent50kmEnergy'] : null;
        $this->energyConsumption = isset($data['energyConsumption']) ? (float) $data['energyConsumption'] : null;

        // Fuel (hybrid vehicles) — oilEndurance uses -1 as "no data" sentinel
        $rawOilEndurance = isset($data['oilEndurance']) ? (float) $data['oilEndurance'] : null;
        $this->oilEndurance = ($rawOilEndurance !== null && $rawOilEndurance < 0) ? null : $rawOilEndurance;

        $rawOilPercent = isset($data['oilPercent']) ? (float) $data['oilPercent'] : null;
        $this->oilPercent = ($rawOilPercent !== null && $rawOilPercent < 0) ? null : $rawOilPercent;

        $this->totalOil = isset($data['totalOil']) ? (float) $data['totalOil'] : null;

        // System indicators — ectValue uses -1 as "no data" sentinel
        $this->powerSystem = isset($data['powerSystem']) ? (int) $data['powerSystem'] : null;
        $this->engineStatus = isset($data['engineStatus']) ? (int) $data['engineStatus'] : null;
        $this->epb = isset($data['epb']) ? (int) $data['epb'] : null;
        $this->eps = isset($data['eps']) ? (int) $data['eps'] : null;
        $this->esp = isset($data['esp']) ? (int) $data['esp'] : null;
        $this->absWarning = isset($data['absWarning']) ? (int) $data['absWarning'] : null;
        $this->svs = isset($data['svs']) ? (int) $data['svs'] : null;
        $this->srs = isset($data['srs']) ? (int) $data['srs'] : null;
        $this->ect = isset($data['ect']) ? (int) $data['ect'] : null;

        $rawEctValue = isset($data['ectValue']) ? (int) $data['ectValue'] : null;
        $this->ectValue = ($rawEctValue !== null && $rawEctValue < 0) ? null : $rawEctValue;

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

    private function parseTimestamp(mixed $timestamp): ?DateTimeInterface
    {
        if ($timestamp === null) {
            return null;
        }

        $ts = (int) $timestamp;
        if ($ts >= 1000000000000) {
            $ts = (int) ($ts / 1000);
        }

        return DateTimeImmutable::createFromFormat('U', (string) $ts) ?: null;
    }

    /**
     * Check if a raw realtime payload appears to contain meaningful data.
     *
     * @param array<string, mixed> $vehicleInfo
     */
    public static function isReadyRaw(array $vehicleInfo): bool
    {
        if ($vehicleInfo === []) {
            return false;
        }

        if (isset($vehicleInfo['onlineState']) && (int) $vehicleInfo['onlineState'] === 2) {
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

        if (isset($vehicleInfo['time']) && (int) $vehicleInfo['time'] > 0) {
            return true;
        }

        return isset($vehicleInfo['enduranceMileage']) && (float) $vehicleInfo['enduranceMileage'] > 0;
    }

    // Convenience properties
    public function isOnline(): bool
    {
        return $this->onlineState === OnlineState::ONLINE;
    }

    public function isCharging(): bool
    {
        return $this->chargingState === ChargingState::CHARGING;
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

        $skipValues = [LockState::UNKNOWN, LockState::UNAVAILABLE];
        $known = array_filter($locks, fn (LockState $lk): bool => !in_array($lk, $skipValues, true));

        if ($known === []) {
            return null;
        }

        return count(array_filter($known, fn ($lk): bool => $lk === LockState::LOCKED)) === count($known);
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

        return array_filter($doors, fn (DoorOpenState $d): bool => $d === DoorOpenState::OPEN) !== [];
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

        return array_filter($windows, fn (WindowState $w): bool => $w === WindowState::OPEN) !== [];
    }

    public function isVehicleOn(): bool
    {
        return $this->powerGear === PowerGear::ON;
    }

    public function isBatteryHeating(): ?bool
    {
        if ($this->batteryHeatState === null) {
            return null;
        }

        return (bool) $this->batteryHeatState;
    }

    public function isSteeringWheelHeating(): bool
    {
        return $this->steeringWheelHeatState === StearingWheelHeat::ON;
    }

    // Getters
    public function getOnlineState(): OnlineState
    {
        return $this->onlineState;
    }

    public function getConnectState(): ConnectState
    {
        return $this->connectState;
    }

    public function getVehicleState(): VehicleState
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

    public function getPowerGear(): PowerGear
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

    public function getAirRunState(): AirCirculationMode
    {
        return $this->airRunState;
    }

    public function getMainSeatHeatState(): SeatHeatVentState
    {
        return $this->mainSeatHeatState;
    }

    public function getMainSeatVentilationState(): SeatHeatVentState
    {
        return $this->mainSeatVentilationState;
    }

    public function getCopilotSeatHeatState(): SeatHeatVentState
    {
        return $this->copilotSeatHeatState;
    }

    public function getCopilotSeatVentilationState(): SeatHeatVentState
    {
        return $this->copilotSeatVentilationState;
    }

    public function getSteeringWheelHeatState(): StearingWheelHeat
    {
        return $this->steeringWheelHeatState;
    }

    public function getLrSeatHeatState(): SeatHeatVentState
    {
        return $this->lrSeatHeatState;
    }

    public function getLrSeatVentilationState(): SeatHeatVentState
    {
        return $this->lrSeatVentilationState;
    }

    public function getLrThirdHeatState(): SeatHeatVentState
    {
        return $this->lrThirdHeatState;
    }

    public function getLrThirdVentilationState(): SeatHeatVentState
    {
        return $this->lrThirdVentilationState;
    }

    public function getRrSeatHeatState(): SeatHeatVentState
    {
        return $this->rrSeatHeatState;
    }

    public function getRrSeatVentilationState(): SeatHeatVentState
    {
        return $this->rrSeatVentilationState;
    }

    public function getRrThirdHeatState(): SeatHeatVentState
    {
        return $this->rrThirdHeatState;
    }

    public function getRrThirdVentilationState(): SeatHeatVentState
    {
        return $this->rrThirdVentilationState;
    }

    public function getChargingState(): ChargingState
    {
        return $this->chargingState;
    }

    public function getChargeState(): ChargingState
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

    public function getLeftFrontDoor(): DoorOpenState
    {
        return $this->leftFrontDoor;
    }

    public function getRightFrontDoor(): DoorOpenState
    {
        return $this->rightFrontDoor;
    }

    public function getLeftRearDoor(): DoorOpenState
    {
        return $this->leftRearDoor;
    }

    public function getRightRearDoor(): DoorOpenState
    {
        return $this->rightRearDoor;
    }

    public function getTrunkLid(): DoorOpenState
    {
        return $this->trunkLid;
    }

    public function getSlidingDoor(): DoorOpenState
    {
        return $this->slidingDoor;
    }

    public function getForehold(): DoorOpenState
    {
        return $this->forehold;
    }

    public function getLeftFrontDoorLock(): LockState
    {
        return $this->leftFrontDoorLock;
    }

    public function getRightFrontDoorLock(): LockState
    {
        return $this->rightFrontDoorLock;
    }

    public function getLeftRearDoorLock(): LockState
    {
        return $this->leftRearDoorLock;
    }

    public function getRightRearDoorLock(): LockState
    {
        return $this->rightRearDoorLock;
    }

    public function getSlidingDoorLock(): LockState
    {
        return $this->slidingDoorLock;
    }

    public function getLeftFrontWindow(): WindowState
    {
        return $this->leftFrontWindow;
    }

    public function getRightFrontWindow(): WindowState
    {
        return $this->rightFrontWindow;
    }

    public function getLeftRearWindow(): WindowState
    {
        return $this->leftRearWindow;
    }

    public function getRightRearWindow(): WindowState
    {
        return $this->rightRearWindow;
    }

    public function getSkylight(): WindowState
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

    public function getTirePressUnit(): TirePressureUnit
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

    public function getEnergyConsumption(): ?float
    {
        return $this->energyConsumption;
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
