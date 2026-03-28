<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function is_array;
use function is_string;

class GpsApi
{
    /**
     * Check if GPS data has meaningful content.
     */
    public static function isGpsInfoReady(array $gpsInfo): bool
    {
        return $gpsInfo !== [] && array_keys($gpsInfo) !== ['requestSerial'];
    }

    /**
     * Fetch a single GPS endpoint, returning [gps_info_array, next_serial].
     *
     * @return array{0: array<string, mixed>, 1: string|null}
     */
    public static function fetchGpsEndpoint(
        string $endpoint,
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin,
        ?string $requestSerial = null
    ): array {
        $inner = Common::buildInnerBase($config, null, $vin, $requestSerial);

        $decoded = TokenJson::postTokenJson(
            $endpoint,
            $config,
            $session,
            $transport,
            $inner,
            null,
            $vin
        );

        if (!is_array($decoded)) {
            return [[], $requestSerial];
        }

        $nextSerial = is_string($decoded['requestSerial'] ?? null) ? $decoded['requestSerial'] : $requestSerial;

        return [$decoded, $nextSerial];
    }
}
