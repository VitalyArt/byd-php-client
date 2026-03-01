<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Exceptions\BydApiException;
use Byd\ApiClient\Exceptions\BydControlPasswordException;
use Byd\ApiClient\Exceptions\BydRemoteControlException;
use Byd\ApiClient\Models\RemoteControlResult;
use Byd\ApiClient\Models\VerifyControlPasswordResponse;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function in_array;
use function is_array;
use function is_string;

class ControlApi
{
    private const VERIFY_CONTROL_PASSWORD_ENDPOINT = '/vehicle/vehicleswitch/verifyControlPassword';
    private const REMOTE_CONTROL_ENDPOINT = '/control/remoteControl';
    private const REMOTE_CONTROL_RESULT_ENDPOINT = '/control/remoteControlResult';

    private const CONTROL_PASSWORD_ERROR_CODES = ['5005', '5006'];
    private const REMOTE_CONTROL_SERVICE_ERROR_CODES = ['1009'];
    private const REMOTE_CONTROL_GENERIC_ERROR_CODES = ['1001'];

    /**
     * Build the inner payload for remote control endpoints.
     *
     * @param array<string, mixed>|null $controlParams
     */
    private static function buildControlInner(
        BydConfig $config,
        string $vin,
        string $commandType,
        ?array $controlParams = null,
        ?string $commandPwd = null,
        ?string $requestSerial = null
    ): array {
        $inner = Common::buildInnerBase($config, null, $vin, $requestSerial);
        $inner['commandPwd'] = $commandPwd ?? '';
        $inner['commandType'] = $commandType;

        if ($controlParams !== null) {
            $inner['controlParamsMap'] = json_encode($controlParams, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }

        return $inner;
    }

    /**
     * Build inner payload for control password verification endpoint.
     */
    private static function buildVerifyControlPasswordInner(
        BydConfig $config,
        string $vin,
        string $commandPwd
    ): array {
        $inner = Common::buildInnerBase($config, null, $vin);
        $inner['commandPwd'] = $commandPwd;
        $inner['functionType'] = 'remoteControl';

        return $inner;
    }

    /**
     * Verify remote control password for a vehicle.
     */
    public static function verifyControlPassword(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin,
        string $commandPwd
    ): VerifyControlPasswordResponse {
        $inner = self::buildVerifyControlPasswordInner($config, $vin, $commandPwd);

        try {
            $data = TokenJson::postTokenJson(
                self::VERIFY_CONTROL_PASSWORD_ENDPOINT,
                $config,
                $session,
                $transport,
                $inner,
                null,
                $vin,
                null,
                self::CONTROL_PASSWORD_ERROR_CODES
            );
        } catch (BydApiException $e) {
            if (in_array($e->getCode(), self::CONTROL_PASSWORD_ERROR_CODES, true)) {
                throw new BydControlPasswordException($e->getMessage(), $e->getCode(), $e->getEndpoint(), $e);
            }

            throw $e;
        }

        $raw = is_array($data) ? $data : [];
        $responseData = array_merge(['vin' => $vin, 'raw' => $raw], $raw);

        return new VerifyControlPasswordResponse($responseData);
    }

    /**
     * Check if remote control result has a terminal state.
     */
    private static function isRemoteControlReady(array $data): bool
    {
        if (empty($data)) {
            return false;
        }

        $controlState = $data['controlState'] ?? null;
        if ($controlState !== null && (int)$controlState !== 0) {
            return true;
        }

        $res = $data['res'] ?? null;
        if ($res !== null) {
            return (int)$res >= 2;
        }

        return isset($data['result']);
    }

    /**
     * Parse raw remote-control result payload into a typed model.
     */
    private static function parseRemoteControlResultData(array $data): RemoteControlResult
    {
        return new RemoteControlResult($data);
    }

    /**
     * Fetch a single control endpoint, returning [result_array, next_serial].
     *
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    private static function fetchControlEndpoint(
        string $endpoint,
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin,
        string $commandType,
        ?array $controlParams = null,
        ?string $commandPwd = null,
        ?string $requestSerial = null
    ): array {
        $inner = self::buildControlInner(
            $config,
            $vin,
            $commandType,
            $controlParams,
            $commandPwd,
            $requestSerial
        );

        $isRemote = in_array($endpoint, [self::REMOTE_CONTROL_ENDPOINT, self::REMOTE_CONTROL_RESULT_ENDPOINT], true);
        $errorCodes = $isRemote ?
            array_merge(self::CONTROL_PASSWORD_ERROR_CODES, self::REMOTE_CONTROL_SERVICE_ERROR_CODES, self::REMOTE_CONTROL_GENERIC_ERROR_CODES) :
            self::CONTROL_PASSWORD_ERROR_CODES;

        try {
            $result = TokenJson::postTokenJson(
                $endpoint,
                $config,
                $session,
                $transport,
                $inner,
                null,
                $vin,
                null,
                $errorCodes
            );
        } catch (BydApiException $e) {
            if (in_array($e->getCode(), self::CONTROL_PASSWORD_ERROR_CODES, true)) {
                throw new BydControlPasswordException($e->getMessage(), $e->getCode(), $e->getEndpoint(), $e);
            }
            if ($isRemote && in_array($e->getCode(), self::REMOTE_CONTROL_SERVICE_ERROR_CODES, true)) {
                throw new BydRemoteControlException($e->getMessage(), $e->getCode(), $e->getEndpoint(), $e);
            }
            if ($isRemote && in_array($e->getCode(), self::REMOTE_CONTROL_GENERIC_ERROR_CODES, true)) {
                throw new BydRemoteControlException($e->getMessage(), $e->getCode(), $e->getEndpoint(), $e);
            }

            throw $e;
        }

        $nextSerial = null;
        if (is_array($result) && isset($result['requestSerial']) && is_string($result['requestSerial'])) {
            $nextSerial = $result['requestSerial'];
        } elseif ($requestSerial !== null) {
            $nextSerial = $requestSerial;
        }

        return [$result, $nextSerial];
    }

    /**
     * Send a remote control command and poll until completion.
     *
     * @param array<string, mixed>|null $controlParams
     */
    public static function pollRemoteControl(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin,
        string $commandType,
        ?array $controlParams = null,
        ?string $commandPwd = null,
        int $pollAttempts = 10,
        float $pollInterval = 1.5
    ): RemoteControlResult {
        // Phase 1: Trigger request
        [$result, $serial] = self::fetchControlEndpoint(
            self::REMOTE_CONTROL_ENDPOINT,
            $config,
            $session,
            $transport,
            $vin,
            $commandType,
            $controlParams,
            $commandPwd
        );

        if (is_array($result) && self::isRemoteControlReady($result)) {
            $parsed = self::parseRemoteControlResultData($result);
            if ($parsed->getControlState() === 2) {
                $msg = $result['message'] ?? $result['msg'] ?? 'controlState=2';

                throw new BydRemoteControlException(
                    "Remote control {$commandType} failed: {$msg}",
                    '2',
                    self::REMOTE_CONTROL_ENDPOINT
                );
            }

            return $parsed;
        }

        if ($serial === null) {
            return self::parseRemoteControlResultData(is_array($result) ? $result : []);
        }

        // Phase 2: Poll for results
        $latest = $result;
        for ($attempt = 1; $attempt <= $pollAttempts; $attempt++) {
            if ($pollInterval > 0) {
                usleep((int)($pollInterval * 1000000));
            }

            try {
                [$latest, $serial] = self::fetchControlEndpoint(
                    self::REMOTE_CONTROL_RESULT_ENDPOINT,
                    $config,
                    $session,
                    $transport,
                    $vin,
                    $commandType,
                    null,
                    null,
                    $serial
                );

                if (is_array($latest) && self::isRemoteControlReady($latest)) {
                    break;
                }
            } catch (BydApiException $e) {
                // Continue polling on API errors
            }
        }

        $parsed = self::parseRemoteControlResultData(is_array($latest) ? $latest : []);
        if ($parsed->getControlState() === 2) {
            $msg = is_array($latest) ? ($latest['message'] ?? $latest['msg'] ?? 'controlState=2') : 'controlState=2';

            throw new BydRemoteControlException(
                "Remote control {$commandType} failed: {$msg}",
                '2',
                self::REMOTE_CONTROL_RESULT_ENDPOINT
            );
        }

        return $parsed;
    }
}
