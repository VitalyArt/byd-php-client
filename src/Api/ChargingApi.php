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
        $inner = Common::buildInnerBase($config, null, $vin);

        $decoded = TokenJson::postTokenJson(
            self::ENDPOINT,
            $config,
            $session,
            $transport,
            $inner,
            null,
            $vin
        );

        return new ChargingStatus(is_array($decoded) ? $decoded : []);
    }
}
