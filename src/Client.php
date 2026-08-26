<?php

declare(strict_types=1);

namespace Byd\ApiClient;

use function array_key_exists;

use Byd\ApiClient\Api\ChargingApi;
use Byd\ApiClient\Api\ControlApi;
use Byd\ApiClient\Api\EnergyApi;
use Byd\ApiClient\Api\GpsApi;
use Byd\ApiClient\Api\HvacApi;
use Byd\ApiClient\Api\Login;
use Byd\ApiClient\Api\PushNotificationsApi;
use Byd\ApiClient\Api\RealtimeApi;
use Byd\ApiClient\Api\SmartChargingApi;
use Byd\ApiClient\Api\VehicleApi;
use Byd\ApiClient\Api\VehicleSettingsApi;
use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Crypto\BangcleCodec;
use Byd\ApiClient\Exceptions\BydException;
use Byd\ApiClient\Exceptions\BydSessionExpiredException;
use Byd\ApiClient\Models\ChargeChangeResult;
use Byd\ApiClient\Models\ChargingStatus;
use Byd\ApiClient\Models\CommandAck;
use Byd\ApiClient\Models\Control\BatteryHeatParams;
use Byd\ApiClient\Models\Control\ClimateScheduleParams;
use Byd\ApiClient\Models\Control\ClimateStartParams;
use Byd\ApiClient\Models\Control\RemoteCommand;
use Byd\ApiClient\Models\Control\SeatClimateParams;
use Byd\ApiClient\Models\EnergyConsumption;
use Byd\ApiClient\Models\GpsInfo;
use Byd\ApiClient\Models\HvacStatus;
use Byd\ApiClient\Models\PushNotificationState;
use Byd\ApiClient\Models\RemoteControlResult;
use Byd\ApiClient\Models\SmartChargingSchedule;
use Byd\ApiClient\Models\Vehicle;
use Byd\ApiClient\Models\VehicleRealtimeData;
use Byd\ApiClient\Models\VerifyControlPasswordResponse;
use Byd\ApiClient\Transport\SecureTransport;
use GuzzleHttp\Client as HttpClient;

use function is_array;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

use function strlen;

/**
 * Main client for the BYD vehicle API.
 */
class Client
{
    private ?HttpClient $httpClient = null;

    private ?BangcleCodec $codec = null;

    private ?SecureTransport $transport = null;

    private ?Session $session = null;

    /** @var array<string, Vehicle> */
    private array $vehiclesByVin = [];

    public function __construct(private BydConfig $config, private ?LoggerInterface $logger = new NullLogger())
    {
    }

    /**
     * Initialise the client transport and codec.
     *
     * Called automatically by `__construct`, but can also be invoked directly
     * when the lifecycle is managed manually.
     *
     * @throws BydException
     */
    public function init(): void
    {
        if (!$this->httpClient instanceof HttpClient) {
            $this->httpClient = new HttpClient([
                'cookies' => true,
            ]);
        }

        if (!$this->codec instanceof BangcleCodec) {
            // Try to find the tables file in the package data directory
            $defaultPath = __DIR__ . '/../data/bangcle_tables.bin';
            $this->codec = new BangcleCodec(file_exists($defaultPath) ? $defaultPath : null);
        }

        if (!$this->transport instanceof SecureTransport) {
            $this->transport = new SecureTransport(
                $this->config,
                $this->codec,
                $this->httpClient,
                $this->logger
            );
        }

        // Load codec tables
        // Note: In a real implementation, this would load the binary tables
        // For now, we're just initializing the codec
    }

    /**
     * Authenticate against the BYD API and obtain session tokens.
     *
     * @throws BydException
     */
    public function login(): void
    {
        // Ensure transport is initialized
        if (!$this->transport instanceof SecureTransport) {
            $this->init();
        }

        $nowMs = (int) (microtime(true) * 1000);
        $outer = Login::buildLoginRequest($this->config, $nowMs);

        if (!$this->transport instanceof SecureTransport) {
            throw new BydException('Transport not initialized');
        }

        $response = $this->transport->postSecure('/app/account/login', $outer);
        $token = Login::parseLoginResponse($response, $this->config->getPassword());

        $ttl = $this->config->getSessionTtl() > 0 ? $this->config->getSessionTtl() : INF;
        $this->session = new Session(
            $token->getUserId(),
            $token->getSignToken(),
            $token->getEncryToken(),
            $ttl
        );
    }

    /**
     * Return an active session, re-authenticating if expired.
     *
     * @throws BydException
     */
    public function ensureSession(): Session
    {
        if ($this->session instanceof Session && !$this->session->isExpired()) {
            return $this->session;
        }

        $this->login();

        if (!$this->session instanceof Session) {
            throw new BydException('Failed to create session');
        }

        return $this->session;
    }

    /**
     * Force session invalidation (next call will re-authenticate).
     */
    public function invalidateSession(): void
    {
        $this->session = null;
    }

    /**
     * Fetch all vehicles associated with the account.
     *
     * @return Vehicle[]
     * @throws BydException
     */
    public function getVehicles(): array
    {
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        $vehicles = VehicleApi::fetchVehicleList($this->config, $session, $transport);
        $this->vehiclesByVin = [];
        foreach ($vehicles as $vehicle) {
            if ($vehicle->getVin() !== '') {
                $this->vehiclesByVin[$vehicle->getVin()] = $vehicle;
            }
        }

        return $vehicles;
    }

    /**
     * Trigger + wait for realtime vehicle data.
     *
     * @param int $pollAttempts Number of polling attempts before giving up
     * @param float $pollInterval Interval between polling attempts in seconds
     * @param float|null $timeout Maximum time to wait for data in seconds (if implemented)
     *
     * @throws BydException
     */
    public function getVehicleRealtime(string $vin, int $pollAttempts = 10, float $pollInterval = 1.5, ?float $timeout = null): VehicleRealtimeData
    {
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        // Phase 1: Trigger
        [$triggerInfo, $serial] = RealtimeApi::fetchRealtimeEndpoint(
            '/vehicleInfo/vehicle/vehicleRealTimeRequest',
            $this->config,
            $session,
            $transport,
            $vin,
            null,
            $this->energyTypeFor($vin)
        );

        $mergedLatest = $triggerInfo;

        if (is_array($triggerInfo) && VehicleRealtimeData::isReadyRaw($triggerInfo)) {
            return new VehicleRealtimeData($triggerInfo);
        }

        if ($serial === null) {
            return new VehicleRealtimeData($mergedLatest);
        }

        // Phase 2: Poll for results
        for ($attempt = 1; $attempt <= $pollAttempts; $attempt++) {
            if ($pollInterval > 0) {
                usleep((int)($pollInterval * 1000000));
            }

            try {
                [$latest, $serial] = RealtimeApi::fetchRealtimeEndpoint(
                    '/vehicleInfo/vehicle/vehicleRealTimeResult',
                    $this->config,
                    $session,
                    $transport,
                    $vin,
                    $serial,
                    $this->energyTypeFor($vin)
                );

                if (is_array($latest)) {
                    $mergedLatest = $latest;
                }

                if (VehicleRealtimeData::isReadyRaw($latest)) {
                    break;
                }
            } catch (BydSessionExpiredException $e) {
                throw $e;
            } catch (BydException) {
                // Continue polling on API errors
            }
        }

        return new VehicleRealtimeData($mergedLatest);
    }

    /**
     * Trigger + wait for GPS info.
     *
     * @param int $pollAttempts Number of polling attempts before giving up
     * @param float $pollInterval Interval between polling attempts in seconds
     * @param float|null $timeout Maximum time to wait for data in seconds (if implemented)
     *
     * @throws BydException
     */
    public function getGpsInfo(string $vin, int $pollAttempts = 10, float $pollInterval = 1.5, ?float $timeout = null): GpsInfo
    {
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        // Phase 1: Trigger
        [$triggerInfo, $serial] = GpsApi::fetchGpsEndpoint(
            '/control/getGpsInfo',
            $this->config,
            $session,
            $transport,
            $vin
        );

        $mergedLatest = $triggerInfo;

        if (is_array($triggerInfo) && GpsInfo::isGpsInfoReady($triggerInfo)) {
            return new GpsInfo($triggerInfo);
        }

        if ($serial === null) {
            return new GpsInfo($mergedLatest);
        }

        // Phase 2: Poll for results
        for ($attempt = 1; $attempt <= $pollAttempts; $attempt++) {
            if ($pollInterval > 0) {
                usleep((int)($pollInterval * 1000000));
            }

            try {
                [$latest, $serial] = GpsApi::fetchGpsEndpoint(
                    '/control/getGpsInfoResult',
                    $this->config,
                    $session,
                    $transport,
                    $vin,
                    $serial
                );

                if (is_array($latest)) {
                    $mergedLatest = $latest;
                }

                if (GpsInfo::isGpsInfoReady($latest)) {
                    break;
                }
            } catch (BydSessionExpiredException $e) {
                throw $e;
            } catch (BydException) {
                // Continue polling on API errors
            }
        }

        return new GpsInfo($mergedLatest);
    }

    // ... rest of the methods remain unchanged ...

    /**
     * Fetch HVAC / climate status.
     *
     * @throws BydException
     */
    public function getHvacStatus(string $vin): HvacStatus
    {
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return HvacApi::fetchHvacStatus($this->config, $session, $transport, $vin);
    }

    /**
     * Fetch charging status.
     *
     * @throws BydException
     */
    public function getChargingStatus(string $vin): ChargingStatus
    {
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ChargingApi::fetchChargingStatus($this->config, $session, $transport, $vin);
    }

    /** Fetch the configured smart-charging schedule from the charging homepage. */
    public function getChargingSchedule(string $vin): SmartChargingSchedule
    {
        $raw = ChargingApi::fetchChargingHomepage($this->config, $this->ensureSession(), $this->requireTransport(), $vin);

        return new SmartChargingSchedule($raw);
    }

    /**
     * Fetch current charging state and schedule in one request.
     *
     * @return array{0: ChargingStatus, 1: SmartChargingSchedule}
     */
    public function getChargingHomepage(string $vin): array
    {
        $raw = ChargingApi::fetchChargingHomepage($this->config, $this->ensureSession(), $this->requireTransport(), $vin);

        return [new ChargingStatus($raw), new SmartChargingSchedule($raw)];
    }

    /**
     * Fetch energy consumption data.
     *
     * @throws BydException
     */
    public function getEnergyConsumption(string $vin): EnergyConsumption
    {
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        $vehicle = $this->vehicleFor($vin);

        return EnergyApi::fetchEnergyConsumption(
            $this->config,
            $session,
            $transport,
            $vin,
            $this->powerTypeFor($vin),
            $vehicle?->getOutModelType() ?: $vehicle?->getModelName()
        );
    }

    /**
     * Fetch push notification state.
     *
     * @throws BydException
     */
    public function getPushState(string $vin): PushNotificationState
    {
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return PushNotificationsApi::fetchPushState($this->config, $session, $transport, $vin);
    }

    /**
     * Enable or disable push notifications.
     *
     * @throws BydException
     */
    public function setPushState(string $vin, bool $enable): CommandAck
    {
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return PushNotificationsApi::setPushState($this->config, $session, $transport, $vin, $enable);
    }

    /**
     * Verify the remote control PIN.
     *
     * @throws BydException
     */
    public function verifyControlPassword(string $vin, ?string $commandPwd = null): VerifyControlPasswordResponse
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::verifyControlPassword($this->config, $session, $transport, $vin, $resolvedPwd);
    }

    /**
     * Lock the vehicle.
     *
     * @throws BydException
     */
    public function lock(string $vin, ?string $commandPwd = null): RemoteControlResult
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::pollRemoteControl($this->config, $session, $transport, $vin, RemoteCommand::LOCK->value, null, $resolvedPwd);
    }

    /**
     * Unlock the vehicle.
     *
     * @throws BydException
     */
    public function unlock(string $vin, ?string $commandPwd = null): RemoteControlResult
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::pollRemoteControl($this->config, $session, $transport, $vin, RemoteCommand::UNLOCK->value, null, $resolvedPwd);
    }

    /**
     * Start climate control with the given parameters.
     *
     * @throws BydException
     */
    public function startClimate(string $vin, ClimateStartParams $params, ?string $commandPwd = null): RemoteControlResult
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::pollRemoteControl($this->config, $session, $transport, $vin, RemoteCommand::START_CLIMATE->value, $params->toControlParamsMap(), $resolvedPwd);
    }

    /**
     * Stop climate control.
     *
     * @throws BydException
     */
    public function stopClimate(string $vin, ?string $commandPwd = null): RemoteControlResult
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::pollRemoteControl($this->config, $session, $transport, $vin, RemoteCommand::STOP_CLIMATE->value, null, $resolvedPwd);
    }

    /**
     * Flash vehicle lights.
     *
     * @throws BydException
     */
    public function flashLights(string $vin, ?string $commandPwd = null): RemoteControlResult
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::pollRemoteControl($this->config, $session, $transport, $vin, RemoteCommand::FLASH_LIGHTS->value, null, $resolvedPwd);
    }

    /**
     * Close all windows.
     *
     * @throws BydException
     */
    public function closeWindows(string $vin, ?string $commandPwd = null): RemoteControlResult
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::pollRemoteControl($this->config, $session, $transport, $vin, RemoteCommand::CLOSE_WINDOWS->value, null, $resolvedPwd);
    }

    /**
     * Activate find-my-car (horn + lights).
     *
     * @throws BydException
     */
    public function findCar(string $vin, ?string $commandPwd = null): RemoteControlResult
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::pollRemoteControl($this->config, $session, $transport, $vin, RemoteCommand::FIND_CAR->value, null, $resolvedPwd);
    }

    /**
     * Schedule climate control.
     *
     * @throws BydException
     */
    public function scheduleClimate(string $vin, ClimateScheduleParams $params, ?string $commandPwd = null): RemoteControlResult
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::pollRemoteControl($this->config, $session, $transport, $vin, RemoteCommand::SCHEDULE_CLIMATE->value, $params->toControlParamsMap(), $resolvedPwd);
    }

    /**
     * Set seat heating/ventilation.
     *
     * @throws BydException
     */
    public function setSeatClimate(string $vin, SeatClimateParams $params, ?string $commandPwd = null): RemoteControlResult
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::pollRemoteControl($this->config, $session, $transport, $vin, RemoteCommand::SEAT_CLIMATE->value, $params->toControlParamsMap(), $resolvedPwd);
    }

    /**
     * Enable or disable battery heating.
     *
     * @throws BydException
     */
    public function setBatteryHeat(string $vin, BatteryHeatParams $params, ?string $commandPwd = null): RemoteControlResult
    {
        $resolvedPwd = $this->resolveCommandPwd($commandPwd);
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return ControlApi::pollRemoteControl($this->config, $session, $transport, $vin, RemoteCommand::BATTERY_HEAT->value, $params->toControlParamsMap(), $resolvedPwd);
    }

    /** Open all windows to BYD's ventilation position. */
    public function openWindows(string $vin, ?string $commandPwd = null): RemoteControlResult
    {
        return ControlApi::pollRemoteControl($this->config, $this->ensureSession(), $this->requireTransport(), $vin, RemoteCommand::OPEN_WINDOWS->value, null, $this->resolveCommandPwd($commandPwd));
    }

    /** Open the powered trunk when supported by the vehicle. */
    public function openTrunk(string $vin, ?string $commandPwd = null): RemoteControlResult
    {
        return ControlApi::pollRemoteControl($this->config, $this->ensureSession(), $this->requireTransport(), $vin, RemoteCommand::OPEN_TRUNK->value, null, $this->resolveCommandPwd($commandPwd));
    }

    /** Close the powered trunk when supported by the vehicle. */
    public function closeTrunk(string $vin, ?string $commandPwd = null): RemoteControlResult
    {
        return ControlApi::pollRemoteControl($this->config, $this->ensureSession(), $this->requireTransport(), $vin, RemoteCommand::CLOSE_TRUNK->value, null, $this->resolveCommandPwd($commandPwd));
    }

    /**
     * Save a smart charging schedule.
     *
     * @throws BydException
     */
    public function saveChargingSchedule(string $vin, SmartChargingSchedule $schedule): CommandAck
    {
        if ($schedule->getTargetSoc() === null ||
            $schedule->getStartHour() === null ||
            $schedule->getStartMinute() === null ||
            $schedule->getEndHour() === null ||
            $schedule->getEndMinute() === null) {
            throw new BydException('SmartChargingSchedule must have all time fields set');
        }

        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return SmartChargingApi::saveChargingSchedule(
            $this->config,
            $session,
            $transport,
            $vin,
            $schedule->getTargetSoc(),
            $schedule->getStartHour(),
            $schedule->getStartMinute(),
            $schedule->getEndHour(),
            $schedule->getEndMinute()
        );
    }

    /**
     * Enable or disable smart charging.
     *
     * @throws BydException
     */
    public function toggleSmartCharging(string $vin, bool $enable): CommandAck
    {
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return SmartChargingApi::toggleSmartCharging($this->config, $session, $transport, $vin, $enable);
    }

    /** Start an active charging session. BYD has no reliable cloud stop command. */
    public function startCharging(string $vin, int $pollAttempts = 6, float $pollInterval = 2.0): ChargeChangeResult
    {
        return SmartChargingApi::startCharging(
            $this->config,
            $this->ensureSession(),
            $this->requireTransport(),
            $vin,
            $pollAttempts,
            $pollInterval
        );
    }

    /**
     * Rename a vehicle.
     *
     * @throws BydException
     */
    public function renameVehicle(string $vin, string $name): CommandAck
    {
        $session = $this->ensureSession();
        $transport = $this->requireTransport();

        return VehicleSettingsApi::renameVehicle($this->config, $session, $transport, $vin, $name);
    }

    /**
     * Get the transport, throwing if not initialized.
     *
     * @throws BydException
     */
    private function requireTransport(): SecureTransport
    {
        if (!$this->transport instanceof SecureTransport) {
            throw new BydException('Client not initialized. Call init() first.');
        }

        return $this->transport;
    }

    private function vehicleFor(string $vin): ?Vehicle
    {
        if (!array_key_exists($vin, $this->vehiclesByVin)) {
            $this->getVehicles();
        }

        return $this->vehiclesByVin[$vin] ?? null;
    }

    private function energyTypeFor(string $vin): string
    {
        $energyType = strtoupper($this->vehicleFor($vin)?->getEnergyType() ?? 'EV');

        return match ($energyType) {
            'PHEV', 'HYBRID', 'HEV' => '1',
            default => '0',
        };
    }

    private function powerTypeFor(string $vin): string
    {
        $energyType = strtoupper($this->vehicleFor($vin)?->getEnergyType() ?? 'EV');

        return match ($energyType) {
            'PHEV', 'HYBRID', 'HEV' => '2',
            default => '0',
        };
    }

    /**
     * Normalize control password (uppercase MD5 hex of PIN).
     */
    private function resolveCommandPwd(?string $commandPwd): string
    {
        if ($commandPwd !== null) {
            $stripped = trim($commandPwd);
            if (strlen($stripped) === 32 && ctype_xdigit($stripped)) {
                return strtoupper($stripped);
            }

            return strtoupper(md5($stripped));
        }

        if ($this->config->getControlPin()) {
            return strtoupper(md5($this->config->getControlPin()));
        }

        return '';
    }
}
