<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function is_array;

class RealtimeApi
{
    /**
     * Fetch a single realtime endpoint, returning [vehicle_info_array, next_serial].
     *
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public static function fetchRealtimeEndpoint(
        string $endpoint,
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin,
        ?string $requestSerial = null,
        string $energyType = '0'
    ): array {
        $nowMs = (int) (microtime(true) * 1000);
        $inner = Common::buildInnerBase($config, $nowMs, $vin, $requestSerial);
        $inner['energyType'] = $energyType;
        $inner['tboxVersion'] = $config->getTboxVersion();

        $decoded = TokenJson::postTokenJson(
            $endpoint,
            $config,
            $session,
            $transport,
            $inner,
            $nowMs,
            $vin
        );

        $vehicleInfo = is_array($decoded) ? $decoded : [];
        $nextSerial = $vehicleInfo['requestSerial'] ?? $requestSerial;

        return [$vehicleInfo, $nextSerial];
    }
}
