<?php

declare(strict_types=1);

namespace Byd\ApiClient\Api;

use Byd\ApiClient\Config\BydConfig;
use Byd\ApiClient\Models\ChargingStatus;
use Byd\ApiClient\Session;
use Byd\ApiClient\Transport\TransportInterface;

use function is_array;

class ChargingApi
{
    private const ENDPOINT = '/control/smartCharge/homePage';

    /**
     * Fetch smart charging status (SOC, charge state, time-to-full).
     */
    public static function fetchChargingStatus(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin
    ): ChargingStatus {
        $decoded = self::fetchChargingHomepage($config, $session, $transport, $vin);

        return new ChargingStatus(is_array($decoded) ? $decoded : []);
    }

    /** @return array<string, mixed> */
    public static function fetchChargingHomepage(
        BydConfig $config,
        Session $session,
        TransportInterface $transport,
        string $vin
    ): array {
        $decoded = TokenJson::postTokenJson(
            self::ENDPOINT,
            $config,
            $session,
            $transport,
            Common::buildInnerBase($config, null, $vin),
            null,
            $vin
        );

        return is_array($decoded) ? $decoded : [];
    }
}
